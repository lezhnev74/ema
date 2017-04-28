<?php
declare(strict_types=1);

namespace EMA\App\Factory;

use Assert\InvalidArgumentException;
use Doctrine\Common\Collections\Collection;
use EMA\App\Account\Command\AddAccount\AddAccount;
use EMA\App\Account\Query\FindAccount\FindAccount;
use EMA\App\Http\Authentication\AuthenticationMiddleware;
use EMA\App\Http\Authentication\BadToken;
use EMA\App\Http\Authentication\JWT;
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
        
        $app->add(function ($req, $res, $next) {
            $response = $next($req, $res);
            
            return $response
                ->withHeader('Access-Control-Allow-Origin', '*')
                ->withHeader('Access-Control-Allow-Headers',
                    'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
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
                if (get_class($exception->getPrevious()) == UnauthorizedException::class) {
                    $response = $response->withJson([
                        'error_code' => 'ACCESS_DENIED',
                        'error_message' => 'You have no access to perform this operation',
                    ], 403);
                } elseif (get_class($exception) == BadToken::class) {
                    $response = $response->withJson([
                        'error_code' => 'BAD_TOKEN',
                        'error_message' => 'You have problem with your token',
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
                } else {
                    $response = $response->withJson([
                        'error_code' => 'SERVER_ERROR',
                        'error_message' => config('app.env') == 'production' ? 'Something went wrong' : $exception->getMessage(),
                    ], 500);
                }
                
                // Log trace string
                $last_exception = $exception;
                $trace_string   = $last_exception->getTraceAsString();
                while ($last_exception = $last_exception->getPrevious()) {
                    $trace_string .= "\n\n========== prev exception ========== \n\n";
                    $trace_string .= $last_exception->getMessage() . "\n";
                    $trace_string .= $last_exception->getTraceAsString();
                };
                log_problem(get_class($exception) . ": " . $exception->getMessage() . " at " . $exception->getFile() . ":" . $exception->getLine(),
                    [
                        'trace' => $trace_string,
                    ]);
                
                log_info('error handler response', ['r' => $response]);
                
                // Why middlewares are skipped when error is thrown?
                // Ref: https://github.com/slimphp/Slim/issues/2041#issuecomment-280632767
                $response = $response->withHeader('Access-Control-Allow-Origin', '*')
                                     ->withHeader('Access-Control-Allow-Headers',
                                         'X-Requested-With, Content-Type, Accept, Origin, Authorization')
                                     ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
                
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
            
            
            // Exchange auth_code from google to app's access_token
            $this->get('/exchange/google',
                function (RequestInterface $request, ResponseInterface $response, array $args) {
                    
                    // exchange google's code to google's access_token
                    $query = parse_query($request->getUri()->getQuery());
                    if (!isset($query['code'])) {
                        throw new \Exception("Code was not found within this request");
                    }
                    
                    
                    $client = container()->get(\Google_Client::class);
                    $client->setRedirectUri($query['redirect_uri']);
                    $answer = $client->fetchAccessTokenWithAuthCode($query['code']);
                    
                    if (!$client->getAccessToken()) {
                        log_problem("Gogle wasnt able to exchange auth code to token", $answer);
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
                    
                    // now find the account by this credentials
                    $account = query_bus_sync_dispatch(new FindAccount(
                        $command->getSocialProviderName(),
                        $command->getSocialProviderId()
                    ));
                    
                    // exchange to the app's access_token
                    $jwt   = new JWT();
                    $token = $jwt->makeToken(new Identity($account['id']));
                    
                    return $response->withJson(['access_token' => $token], 200);
                    
                })->setName("api.google.exchange");
            
            
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
                    
                    log_info("Search", [
                        'query' => $args['query'],
                        'found_results' => count($result->toArray()),
                    ]);
                    
                    return $response;
                    
                })->setName('api.notes.search');
            
            
            $this->post('', function (RequestInterface $request, ResponseInterface $response, array $args) {
                
                $id         = new Identity();
                $owner_id   = current_authenticated_user_id();
                $input_data = json_decode($request->getBody()->getContents(), true);
                $text       = new NoteText($input_data['text']);
                $command    = new PostNewNote($text, $id, $owner_id);
                command_bus()->dispatch($command);
                
                return $response->withJson(['note_id' => $id->getAsString()]);
            })->setName('api.notes.add');
            
            
            $this->post('/{note_id:[0-9a-zA-Z-]+}',
                function (RequestInterface $request, ResponseInterface $response, array $args) {
                    
                    $input_data = json_decode($request->getBody()->getContents(), true);
                    $text       = new NoteText($input_data['text']);
                    $note_id    = new Identity($args['note_id']);
                    $command    = new ModifyNote($text, $note_id);
                    command_bus()->dispatch($command);
                    
                    return $response->withJson(['note_id' => $args['note_id']]);
                })->setName('api.notes.update');
            
            
            $this->delete('/{note_id:[0-9a-zA-Z-]+}',
                function (RequestInterface $request, ResponseInterface $response, array $args) {
                    
                    $note_id = new Identity($args['note_id']);
                    $command = new DeleteNote($note_id);
                    command_bus()->dispatch($command);
                    
                    return $response->withJson(['note_id' => $args['note_id']]);
                    
                })->setName('api.notes.delete');
            
            
        })->add(new AuthenticationMiddleware());
        
        
    }
}