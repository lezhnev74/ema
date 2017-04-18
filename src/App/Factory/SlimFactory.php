<?php
declare(strict_types=1);

namespace EMA\App\Factory;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Slim\App;

final class SlimFactory
{
    
    function __invoke(ContainerInterface $container): App
    {
        $app = new App($container);
        
        $this->init_routes($app);
        
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
                return $response->withStatus(200)->write('ok');
            })->setName('api.notes');
            
            $this->post('/', function (RequestInterface $request, ResponseInterface $response, array $args) {
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
        });
        
        
    }
}