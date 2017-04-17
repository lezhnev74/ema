<?php


use function DI\factory;
use function DI\object;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use EMA\Infrastructure\Factory\DoctrineConnection;
use Interop\Container\ContainerInterface;
use Prooph\ServiceBus\Container\CommandBusFactory;
use Prooph\ServiceBus\Container\EventBusFactory;

return [
    
    // Config
    'config' => factory(function (ContainerInterface $container) {
        $config = [];
        $config = array_merge($config, config('prooph_config')); //add prooph config in there
        
        return $config;
    }),
    
    
    // APP LAYER --------------------------------------
    
    // Command Bus
    \Prooph\ServiceBus\CommandBus::class => factory(CommandBusFactory::class),
    \Prooph\ServiceBus\Plugin\Guard\AuthorizationService::class => object(\EMA\App\Authorization\AuthorizationService::class),
    \Prooph\ServiceBus\Plugin\Guard\RouteGuard::class => factory(\Prooph\ServiceBus\Container\Plugin\Guard\RouteGuardFactory::class),
    //\Prooph\ServiceBus\Plugin\Guard\FinalizeGuard::class => factory(\Prooph\ServiceBus\Container\Plugin\Guard\FinalizeGuardFactory::class),
    // Event bus
    \Prooph\ServiceBus\EventBus::class => factory(EventBusFactory::class),
    
    // INFRASTRUCTURE LAYER --------------------------
    
    \Doctrine\DBAL\Connection::class => factory([DoctrineConnection::class, 'default']),
    Configuration::class => factory([DoctrineConnection::class, 'migration_config']),
];

