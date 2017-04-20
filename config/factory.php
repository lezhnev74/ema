<?php


use function DI\factory;
use function DI\get;
use function DI\object;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use EMA\App\Factory\SlimFactory;
use EMA\App\Query\Note\NoteFinder;
use EMA\Infrastructure\Factory\DoctrineConnection;
use EMA\Infrastructure\Note\Finder\DoctrineNoteFinder;
use Interop\Container\ContainerInterface;
use Prooph\ServiceBus\Container\CommandBusFactory;
use Prooph\ServiceBus\Container\EventBusFactory;
use Prooph\ServiceBus\Container\QueryBusFactory;
use Slim\App;


return [
    
    // Config (following Interop/Config rules)
    'config' => factory(function (ContainerInterface $container) {
        $config = [];
        $config = array_merge($config, config('prooph_config')); //add prooph config in there
        
        return $config;
    }),
    
    
    //
    // APP LAYER --------------------------------------
    //
    
    // Identity object
    'authenticated_user_identity' => null,
    
    
    // Command Bus
    \Prooph\ServiceBus\CommandBus::class => factory(CommandBusFactory::class),
    \Prooph\ServiceBus\Plugin\Guard\AuthorizationService::class =>
        object(\EMA\App\Authorization\AuthorizationService::class)
            ->constructorParameter('authenticated_user_identity', get('authenticated_user_identity')),
    \Prooph\ServiceBus\Plugin\Guard\RouteGuard::class => factory(\Prooph\ServiceBus\Container\Plugin\Guard\RouteGuardFactory::class),
    //\Prooph\ServiceBus\Plugin\Guard\FinalizeGuard::class => factory(\Prooph\ServiceBus\Container\Plugin\Guard\FinalizeGuardFactory::class),
    // Event bus
    \Prooph\ServiceBus\EventBus::class => factory(EventBusFactory::class),
    // Query bus
    \Prooph\ServiceBus\QueryBus::class => factory(QueryBusFactory::class),
    
    App::class => factory(SlimFactory::class),
    NoteFinder::class => object(DoctrineNoteFinder::class),
    
    
    //
    // INFRASTRUCTURE LAYER --------------------------
    //
    
    
    \Doctrine\DBAL\Connection::class => factory([DoctrineConnection::class, 'default']),
    Configuration::class => factory([DoctrineConnection::class, 'migration_config']),
];

