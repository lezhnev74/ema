<?php
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Tools\Console\Command\AbstractCommand;
use Doctrine\DBAL\Version;
use Symfony\Component\Console\Application;

require __DIR__ . "/../bootstrap/autoload.php";

$cli = new Application('Doctrine Migration Tool', Version::VERSION);
$cli->setCatchExceptions(true);

$db        = container()->get(Connection::class);
$helperSet = new \Symfony\Component\Console\Helper\HelperSet([
    'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($db),
    'dialog' => new \Symfony\Component\Console\Helper\QuestionHelper(),
]);
$cli->setHelperSet($helperSet);


$commands = [
    // Migrations Commands
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\DiffCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\ExecuteCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\GenerateCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\StatusCommand(),
    new \Doctrine\DBAL\Migrations\Tools\Console\Command\VersionCommand(),
];
array_map(function (AbstractCommand $cmd) use ($db) {
    $configuration = container()->get(Configuration::class);
    $cmd->setMigrationConfiguration($configuration);
}, $commands);

$cli->addCommands($commands);

$cli->run();