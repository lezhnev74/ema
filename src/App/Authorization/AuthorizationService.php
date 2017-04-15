<?php
declare(strict_types=1);

namespace EMA\App\Authorization;

use EMA\Domain\Foundation\Command\Authorizer;
use Prooph\ServiceBus\Plugin\Guard\AuthorizationService as ProophAuthorizationService;
use Psr\Container\ContainerInterface;

final class AuthorizationService implements ProophAuthorizationService
{
    /** @var  ContainerInterface */
    private $container;
    
    /**
     * AuthorizationService constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container) { $this->container = $container; }
    
    
    /**
     * isGranted - deny all strategy by default
     *
     *
     * @param string $messageName
     * @param null   $context
     *
     * @return bool
     */
    public function isGranted(string $messageName, $context = null): bool
    {
        
        //
        // 1. Is it a FQCN message?
        //
        if (class_exists($messageName)) {
            //
            // 1. Check if implicit authorizer class exists for this FQCN
            //
            if ($authorizer = $this->getImplicitAuthorizer($messageName)) {
                return !$authorizer->denied();
            }
        }
        
        //
        // 2. TODO what other strategies might be?
        //
        
        
        return false;
    }
    
    
    /**
     * checkImplicit
     * Will try to find authorizer class in the same namespace as original message
     *
     *
     * @param $messageName
     *
     * @return Authorizer
     */
    private function getImplicitAuthorizer($messageName): Authorizer
    {
        $authorizer_fqcn = $messageName . "Authorizer";
        
        if ($this->container->has($authorizer_fqcn)) {
            return $this->container->get($authorizer_fqcn);
        }
        
        return null;
    }
    
}