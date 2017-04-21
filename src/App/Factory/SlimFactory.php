<?php
declare(strict_types=1);

namespace EMA\App\Factory;

use Doctrine\Common\Collections\Collection;
use EMA\App\Http\Authentication\AuthenticationMiddleware;
use EMA\App\Query\Note\AllNotes\AllNotes;
use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Commands\PostNewNote\PostNewNote;
use EMA\Domain\Note\Model\VO\NoteText;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

final class SlimFactory
{
    
    function __invoke(ContainerInterface $container): App
    {
        $app = new App();
        
        $this->init_routes($app);
        
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
                
                });
            
            $this->get('/callback/twitter',
                function (RequestInterface $request, ResponseInterface $response, array $args) {
                
                });
            
        });
        
        //
        // NOTES CRUD
        //
        $app->group('/api/notes', function () {
            
            $this->get('/', function (RequestInterface $request, ResponseInterface $response, array $args) {
                
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
    
            $this->get('/search', function (RequestInterface $request, ResponseInterface $response, array $args) {
        
                // Query all available notes
                $query = new AllNotes(current_authenticated_user_id());
                /** @var Collection $result */
                $result          = query_bus_sync_dispatch($query);
                $result_filtered = $result->filter(function (array $entry) {
                    return $entry['owner_id'] == current_authenticated_user_id()->getAsString();
                });
        
                $response = $response->withJson($result_filtered->toArray(), 200);
        
                return $response;
        
            })->setName('api.notes.search');
            
            $this->post('/', function (RequestInterface $request, ResponseInterface $response, array $args) {
                
                $id         = new Identity();
                $owner_id   = current_authenticated_user_id();
                $input_data = json_decode($request->getBody()->getContents(), true);
                $text       = new NoteText($input_data['text']);
                $command    = new PostNewNote($text, $id, $owner_id);
                
                return $response->withStatus(200)->write('ok');
            })->setName('api.notes.add');
            
            $this->put('/{note_id:[a-zA-Z]+}',
                function (RequestInterface $request, ResponseInterface $response, array $args) {
                    return $response->withStatus(200)->write('ok');
                })->setName('api.notes.update');
            
            $this->delete('/{note_id:[a-zA-Z]+}',
                function (RequestInterface $request, ResponseInterface $response, array $args) {
                    return $response->withStatus(200)->write('ok');
                })->setName('api.notes.delete');
        })->add(new AuthenticationMiddleware());
        
        
    }
}