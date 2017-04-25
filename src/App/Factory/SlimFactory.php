<?php
declare(strict_types=1);

namespace EMA\App\Factory;

use Assert\InvalidArgumentException;
use Doctrine\Common\Collections\Collection;
use EMA\App\Account\Command\AddAccount\AddAccount;
use EMA\App\Http\Authentication\AuthenticationMiddleware;
use EMA\App\Note\Query\AllNotes\AllNotes;
use EMA\App\Note\Query\SearchNotes\SearchNotes;
use EMA\Domain\Foundation\Exception\DomainProblem;
use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Commands\DeleteNote\DeleteNote;
use EMA\Domain\Note\Commands\ModifyNote\ModifyNote;
use EMA\Domain\Note\Commands\PostNewNote\PostNewNote;
use EMA\Domain\Note\Model\VO\NoteText;
use function GuzzleHttp\Psr7\parse_query;
use Interop\Container\ContainerInterface;
use Prooph\ServiceBus\Plugin\Guard\UnauthorizedException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Route;

final class SlimFactory
{
    
    function __invoke(ContainerInterface $container): App
    {
        $app = new App();
        
        $this->init_routes($app);
        
        $this->addErrorHandler($app);
        
        //
        // Trailing slash fix
        // Ref: https://www.slimframework.com/docs/cookbook/route-patterns.html
        $app->add(function (Request $request, Response $response, callable $next) {
            $uri  = $request->getUri();
            $path = $uri->getPath();
            if ($path != '/' && substr($path, -1) == '/') {
                // permanently redirect paths with a trailing slash
                // to their non-trailing counterpart
                $uri = $uri->withPath(substr($path, 0, -1));
                
                if ($request->getMethod() == 'GET') {
                    return $response->withRedirect((string)$uri, 301);
                } else {
                    return $next($request->withUri($uri), $response);
                }
            }
            
            
            return $next($request, $response);
        });
        
        return $app;
    }
    
    
    private function addErrorHandler(App $app)
    {
        $c                 = $app->getContainer();
        $c['errorHandler'] = function ($c) {
            return function (ServerRequestInterface $request, ResponseInterface $response, \Throwable $exception) use (
                $c
            ) {
                // Default response
                $response = $response->withJson([
                    'error_code' => 'SERVER_ERROR',
                    'error_message' => config('app.env') == 'production' ? 'Something went wrong' : $exception->getMessage(),
                ], 500);
                
                
                if (get_class($exception->getPrevious()) == UnauthorizedException::class) {
                    $response = $response->withJson([
                        'error_code' => 'ACCESS_DENIED',
                        'error_message' => 'You have no access to perform this operation',
                    ], 403);
                } elseif (get_class($exception->getPrevious()) == InvalidArgumentException::class) {
                    /** @var InvalidArgumentException $e */
                    $e = $exception->getPrevious();
                    
                    $response = $response->withJson([
                        'error_code' => 'INVALID_DATA',
                        'error_message' => $e->getMessage(),
                    ], 422);
                } elseif (get_class($exception->getPrevious()) == DomainProblem::class) {
                    /** @var DomainProblem $e */
                    $e = $exception->getPrevious();
                    
                    $response = $response->withJson([
                        'error_code' => $e->getProblemCode(),
                        'error_message' => $e->getMessage(),
                    ], 422);
                };
                
                
                // Log trace string
                $last_exception = $exception;
                $trace_string   = $last_exception->getTraceAsString();
                while ($last_exception = $last_exception->getPrevious()) {
                    $trace_string .= "\n\n========== prev exception ========== \n\n";
                    $trace_string .= $last_exception->getMessage() . "\n";
                    $trace_string .= $last_exception->getTraceAsString();
                };
                log_problem(get_class($exception) . ": " . $exception->getMessage(), [
                    'trace' => $trace_string,
                ]);
                
                return $response;
            };
        };
    }
    
    /**
     * Init http routes
     *
     *
     * @param App $app
     *
     * @return void
     */
    private function init_routes(App $app)
    {
        
        //
        // AUTHORIZATION
        //
        $app->group('/api/auth', function () {
            
            
            $this->get('/callback/google',
                function (RequestInterface $request, ResponseInterface $response, array $args) {
                    
                    // exchange google's code to google's access_token
                    $query = parse_query($request->getUri()->getQuery());
                    if (!isset($query['code'])) {
                        throw new \Exception("Code was not found within this request");
                    }
                    
                    
                    $client = container()->get(\Google_Client::class);
                    $client->setRedirectUri(
                        config('app.base_url')
                        . $this->get('router')->pathFor('api.google.callback')
                    );
                    $client->fetchAccessTokenWithAuthCode($query['code']);
                    
                    if (!$client->getAccessToken()) {
                        throw new \Exception("Unable to exchange code to token");
                    }
                    
                    // get account's unique_id
                    // Ref: https://developers.google.com/api-client-library/php/guide/aaa_idtoken
                    $payload = $client->verifyIdToken();
                    if (!isset($payload['sub'])) {
                        throw new \Exception("Google access_token has no 'sub' item");
                    }
                    
                    // add new user
                    $command = new AddAccount("google", $payload['sub']);
                    command_bus()->dispatch($command);
                    
                    // exchange to the app's access_token
                    
                })->setName("api.google.callback");
            
            
        });
        
        //
        // NOTES CRUD
        //
        $app->group('/api/notes', function () {
            
            $this->get('', function (RequestInterface $request, ResponseInterface $response, array $args) {
                
                // Query all available notes
                $query = new AllNotes(current_authenticated_user_id());
                /** @var Collection $result */
                $result          = query_bus_sync_dispatch($query);
                $result_filtered = $result->filter(function (array $entry) {
                    return $entry['owner_id'] == current_authenticated_user_id()->getAsString();
                });
                
                $response = $response->withJson($result_filtered->toArray(), 200);
                
                return $response;
                
            })->setName('api.notes');
            
            
            $this->get('/search/{query}',
                function (RequestInterface $request, ResponseInterface $response, array $args) {
                    
                    // Query all available notes
                    $query = new SearchNotes(current_authenticated_user_id(), $args['query']);
                    /** @var Collection $result */
                    $result = query_bus_sync_dispatch($query);
                    
                    $response = $response->withJson(array_values($result->toArray()), 200);
                    
                    return $response;
                    
                })->setName('api.notes.search');
            
            
            $this->post('', function (RequestInterface $request, ResponseInterface $response, array $args) {
                
                $id         = new Identity();
                $owner_id   = current_authenticated_user_id();
                $input_data = json_decode($request->getBody()->getContents(), true);
                $text       = new NoteText($input_data['text']);
                $command    = new PostNewNote($text, $id, $owner_id);
                command_bus()->dispatch($command);
                
                return $response->withStatus(200)->write('ok');
            })->setName('api.notes.add');
            
            
            $this->post('/{note_id:[0-9a-zA-Z-]+}',
                function (RequestInterface $request, ResponseInterface $response, array $args) {
                    
                    $input_data = json_decode($request->getBody()->getContents(), true);
                    $text       = new NoteText($input_data['text']);
                    $note_id    = new Identity($args['note_id']);
                    $command    = new ModifyNote($text, $note_id);
                    command_bus()->dispatch($command);
                    
                    return $response->withStatus(200)->write('ok');
                })->setName('api.notes.update');
            
            
            $this->delete('/{note_id:[0-9a-zA-Z-]+}',
                function (RequestInterface $request, ResponseInterface $response, array $args) {
                    
                    $note_id = new Identity($args['note_id']);
                    $command = new DeleteNote($note_id);
                    command_bus()->dispatch($command);
                    
                    return $response->withStatus(200)->write('ok');
                    
                })->setName('api.notes.delete');
            
            
        })->add(new AuthenticationMiddleware());
        
        
    }
}