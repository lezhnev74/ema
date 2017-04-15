<?php
declare(strict_types=1);

namespace EMA\App\Factory;

use DirectRouter\DirectRouter;
use Prooph\ServiceBus\Plugin\Guard\RouteGuard;
use Prooph\ServiceBus\Plugin\ServiceLocatorPlugin;
use Psr\Container\ContainerInterface;

final class EventBus
{
    function create(ContainerInterface $container): \Prooph\ServiceBus\EventBus
    {
        $bus = new \Prooph\ServiceBus\EventBus();
        
        // Todo add plugins
        
        return $bus;
    }
}