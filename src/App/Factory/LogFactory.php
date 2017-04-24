<?php
declare(strict_types=1);

namespace EMA\App\Factory;

use Interop\Container\ContainerInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

final class LogFactory
{
    
    function __invoke(ContainerInterface $c): Logger
    {
        $prefix = config('app.storage_path');
        $path   = $prefix . '/logs/app.log';
        @mkdir(dirname($path), 0770);
        
        
        $handler = new StreamHandler($path);
        $handler->setFormatter(new LineFormatter(null, null, true));
        
        $log = new Logger(config('app.env'));
        $log->pushHandler($handler);
        
        return $log;
    }
    
}