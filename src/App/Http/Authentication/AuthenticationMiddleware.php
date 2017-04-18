<?php
declare(strict_types=1);

namespace EMA\App\Http\Authentication;

use EMA\Domain\Foundation\VO\Identity;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class AuthenticationMiddleware
{
    /**
     * Check header and parse the token
     * If valid then authenticate user
     *
     * @param  \Psr\Http\Message\ServerRequestInterface $request  PSR7 request
     * @param  \Psr\Http\Message\ResponseInterface      $response PSR7 response
     * @param  callable                                 $next     Next middleware
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next)
    {
        
        if ($auth_line = $request->getHeaderLine('Authorization')) {
            if (preg_match("#^Bearer (.+)$#", $auth_line, $p)) {
                $token = $p[1];
                
                // is it a valid token?
                
                if (false) {
                    $id = new Identity();
                    container()->set('authenticated_user_identity', $id); // authenticate user
                }
            }
        }
        
        $response = $next($request, $response);
        
        return $response;
    }
}