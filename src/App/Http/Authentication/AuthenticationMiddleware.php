<?php
declare(strict_types=1);

namespace EMA\App\Http\Authentication;

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
                $jwt = new JWT();
                $id = $jwt->parseToken($token);
                
                container()->set('authenticated_user_identity', $id); // authenticate user
                
            }
        }
        
        if (is_null(current_authenticated_user_id())) {
            $response = $response->withStatus(403);
            
            return $response;
        }
        
        $response = $next($request, $response);
        
        return $response;
    }
}