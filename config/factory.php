<?php


use function DI\factory;
use function DI\object;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use EMA\Infrastructure\Factory\DoctrineConnection;

return [
    // APP
    
    // Command Bus
    \Prooph\ServiceBus\CommandBus::class => factory([\EMA\App\Factory\CommandBus::class, 'create']),
    \Prooph\ServiceBus\Plugin\Guard\AuthorizationService::class => object(\EMA\App\Authorization\AuthorizationService::class),
    \Prooph\ServiceBus\Plugin\Guard\RouteGuard::class => factory(\Prooph\ServiceBus\Container\Plugin\Guard\RouteGuardFactory::class),
    //\Prooph\ServiceBus\Plugin\Guard\FinalizeGuard::class => factory(\Prooph\ServiceBus\Container\Plugin\Guard\FinalizeGuardFactory::class),
    // Event bus
    \Prooph\ServiceBus\EventBus::class => factory([\EMA\App\Factory\EventBus::class, 'create']),
    
    // INFRASTRUCTURE
    \Doctrine\DBAL\Connection::class => factory([DoctrineConnection::class, 'default']),
    Configuration::class => factory([DoctrineConnection::class, 'migration_config']),
];

