<?php
declare(strict_types=1);

namespace EMA\App\Factory;

use DirectRouter\DirectRouter;
use Prooph\ServiceBus\Plugin\Guard\RouteGuard;
use Prooph\ServiceBus\Plugin\ServiceLocatorPlugin;
use Psr\Container\ContainerInterface;

final class CommandBus
{
    function create(ContainerInterface $container): \Prooph\ServiceBus\CommandBus
    {
        $bus = new \Prooph\ServiceBus\CommandBus();
        
        // Implicit same namesapce router
        $router = new DirectRouter();
        $router->attachToMessageBus($bus);
        
        // Locate services through the container
        $locator = new ServiceLocatorPlugin(container());
        $locator->attachToMessageBus($bus);
        
        // Route guard (authorization)
        $route_guard = $container->get(RouteGuard::class);
        $route_guard->attachToMessageBus($bus);
        
        return $bus;
    }
}