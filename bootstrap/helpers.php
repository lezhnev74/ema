<?php
use DirectRouter\DirectRouter;
use Doctrine\Common\Cache\ApcuCache;
use DummyConfigLoader\Config;
use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Model\VO\NoteText;
use Prooph\ServiceBus\CommandBus;
use Prooph\ServiceBus\Container\CommandBusFactory;
use Prooph\ServiceBus\Container\EventBusFactory;
use Prooph\ServiceBus\EventBus;
use Prooph\ServiceBus\Plugin\InvokeStrategy\HandleCommandStrategy;
use Prooph\ServiceBus\Plugin\ServiceLocatorPlugin;
use Prooph\ServiceBus\QueryBus;
use Psr\Container\ContainerInterface;
use Symfony\Component\Translation\Tests\StringClass;
use voku\helper\UTF8;

if (!function_exists('value')) {
    /**
     * Return the default value of the given value.
     *
     * @author This code is taken from Laravel's helper file
     *
     * @param  mixed $value
     *
     * @return mixed
     */
    function value($value)
    {
        return $value instanceof Closure ? $value() : $value;
    }
}


if (!function_exists('env')) {
    /**
     * Gets the value of an environment variable. Supports boolean, empty and null.
     *
     * @author This code is taken from Laravel's helper file
     *
     * @param  string $key
     * @param  mixed  $default
     *
     * @return mixed
     */
    function env($key, $default = null)
    {
        static $dotenv = null;
        if (!$dotenv) {
            $dotenv = new Dotenv\Dotenv(__DIR__ . "/../");
            $dotenv->load();
        }
        
        $value = getenv($key);
        
        if ($value === false) {
            return value($default);
        }
        
        switch (UTF8::strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'null':
            case '(null)':
                return null;
        }
        
        if (UTF8::strlen($value) > 1 && UTF8::str_starts_with($value, '"') && UTF8::str_ends_with($value, '"')) {
            return UTF8::substr($value, 1, -1);
        }
        
        return $value;
    }
}

if (!function_exists('config')) {
    function config($key, $default = null)
    {
        static $config = null;
        if (is_null($config)) {
            $config = new Config(__DIR__ . '/../config');
        }
        
        return $config->get($key, $default);
    }
}

if (!function_exists('storage_path')) {
    function storage_path($relative = '')
    {
        if (UTF8::substr($relative, 0, 1) != DIRECTORY_SEPARATOR) {
            $relative = '/' . $relative;
        }
        
        return config('path.storage', __DIR__ . '/../storage') . $relative;
    }
}


if (!function_exists('container')) {
    function container(bool $restart = false): ContainerInterface
    {
        static $container = null;
        
        if (is_null($container) || $restart) {
            $container = null;
            $builder   = new \DI\ContainerBuilder();
            $builder->useAutowiring(true);
            $builder->useAnnotations(false);
            
            $env = config('app.env');
            $builder->addDefinitions(config('factory'));
            try {
                $builder->addDefinitions(config('factory_' . $env));
            } catch (\Exception $e) {
                // config is not available for current environment
            }
            
            if ($env == "production") {
                $builder->setDefinitionCache(new ApcuCache());
            }
            
            $container = $builder->build();
            
        }
        
        return $container;
    }
}

if (!function_exists('command_bus')) {
    function command_bus(): CommandBus
    {
        return container()->get(CommandBus::class);
    }
}

if (!function_exists('event_bus')) {
    function event_bus(): EventBus
    {
        return container()->get(EventBus::class);
    }
}

if (!function_exists('query_bus')) {
    function query_bus(): QueryBus
    {
        return container()->get(QueryBus::class);
    }
}

if (!function_exists('query_bus_sync_dispatch')) {
    function query_bus_sync_dispatch($query)
    {
        $value = null;
        query_bus()->dispatch($query)->then(function ($result) use (&$value) {
            $value = $result;
        }, function (Throwable $problem) {
            throw $problem;
        })->done();
        
        return $value;
    }
}

if (!function_exists('current_authenticated_user_id')) {
    /**
     * current_authenticated_user_id
     *
     *
     * @return Identity|null
     */
    function current_authenticated_user_id()
    {
        return container()->get('authenticated_user_identity');
    }
}

