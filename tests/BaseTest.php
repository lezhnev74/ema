<?php
declare(strict_types=1);

namespace EMA\Tests;

use DI\Container;
use function DI\object;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Migrations\Configuration\Configuration;
use Doctrine\DBAL\Migrations\Tools\Console\Command\AbstractCommand;
use Doctrine\DBAL\Migrations\Tools\Console\Helper\ConfigurationHelper;
use Doctrine\DBAL\Version;
use EMA\App\Http\Authentication\JWT;
use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Model\Collection\NoteCollection;
use EMA\Tests\Services\AuthorizationFakeService;
use EMA\Tests\Services\BusEventLogger;
use PHPUnit\Framework\TestCase;
use Prooph\ServiceBus\Plugin\Guard\AuthorizationService;
use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Output\OutputInterface;

class BaseTest extends TestCase
{
    protected function restartContainer()
    {
        container(true);
    }
    
    protected function setAuthorizationAs(bool $result)
    {
        container()->set(
            AuthorizationService::class,
            new AuthorizationFakeService($result)
        );
    }
    
    protected function setAuthenticatedUser(Identity $id)
    {
        container()->set('authenticated_user_identity', $id);
    }
    
    /**
     * migrate
     * It is surprisingly hard to use doctrine migrations from code
     *
     * @return void
     */
    protected function migrate()
    {
        $cli = new Application('Doctrine Migration Tool', Version::VERSION);
        $cli->setCatchExceptions(true);
        
        $db        = container()->get(Connection::class);
        $helperSet = new \Symfony\Component\Console\Helper\HelperSet([
            'db' => new \Doctrine\DBAL\Tools\Console\Helper\ConnectionHelper($db),
            'dialog' => new \Symfony\Component\Console\Helper\QuestionHelper(),
        ]);
        $cli->setHelperSet($helperSet);
        
        
        $commands = [
            new \Doctrine\DBAL\Migrations\Tools\Console\Command\MigrateCommand(),
        ];
        array_map(function (AbstractCommand $cmd) use ($db) {
            $configuration = container()->get(Configuration::class);
            $cmd->setMigrationConfiguration($configuration);
        }, $commands);
        
        $cli->addCommands($commands);
        
        $input = new ArrayInput([
            'migrations:migrate',
        ]);
        $input->setInteractive(false);
        $cli->doRun($input, new NullOutput());
        
    }
    
    protected function getEventBusLogger(): BusEventLogger
    {
        $bus_event_logger = new BusEventLogger();
        $bus_event_logger->attachToMessageBus(event_bus());
        
        return $bus_event_logger;
        
    }
    
    
    protected function sendHttp(
        string $method,
        string $path,
        array $data = [],
        Identity $user_id = null,
        App $app = null
    ): Response {
        
        if (!$app) {
            $app = container()->get(App::class);
        }
        
        $headers = ['Content-Type' => 'application/json'];
        if ($user_id) {
            $jwt                      = new JWT();
            $token                    = $jwt->makeToken($user_id);
            $headers['Authorization'] = 'Bearer ' . $token;
        }
        
        $response = $response = $app->subRequest(
            $method,
            $path,
            '',
            $headers,
            [],
            json_encode($data));
        
        return $response;
    }
    
}
