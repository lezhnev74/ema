<?php
declare(strict_types=1);

namespace EMA\Infrastructure\Factory;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Psr\Container\ContainerInterface;

final class DoctrineConnection
{
    public function default(ContainerInterface $container): Connection
    {
        $config           = new \Doctrine\DBAL\Configuration();
        $connectionParams = config('database.dbal.' . config('database.dbal.default'));
        $conn             = \Doctrine\DBAL\DriverManager::getConnection($connectionParams, $config);
        
        return $conn;
    }
    
    public function migration_config(ContainerInterface $container): Configuration
    {
        $db = $container->get(Connection::class);
        
        $configuration = new Configuration($db);
        $configuration->setMigrationsTableName('migrations');
        $configuration->setMigrationsDirectory('migrations');
        $configuration->setMigrationsNamespace('EMA\Migrations');
        $configuration->registerMigrationsFromDirectory('migrations');
        
        return $configuration;
    }
}