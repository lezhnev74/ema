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
use Psr\Http\Message\RequestInterface;
use Slim\App;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Http\Response;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;

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
    
    protected function sendHttpRequest(
        RequestInterface $request,
        App $app = null
    ): Response {
        
        if (!$app) {
            $app = container()->get(App::class);
        }
        
        $response = $app->process($request, new Response());
        
        return $response;
    }
    
    protected function getRequest(
        string $method,
        string $path,
        array $data = [],
        Identity $current_user_id = null
    ): Request {
        $method = strtoupper($method);
        
        $request = Request::createFromEnvironment(Environment::mock([
            'REQUEST_METHOD' => strtoupper($method),
            'REQUEST_URI' => $path,
            'QUERY_STRING' => ($method == "GET") ? http_build_query($data) : "",
        ]));
        
        $request = $request->withHeader('Content-Type', 'application/json');
        
        if ($method == "POST") {
            $request->getBody()->write(json_encode($data));
        }
        
        if ($current_user_id) {
            $jwt     = new JWT();
            $token   = $jwt->makeToken($current_user_id);
            $request = $request->withHeader('Authorization', 'Bearer ' . $token);
        }
        
        return $request;
    }
    
}
