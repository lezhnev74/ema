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
        $path   = $prefix . '/logs/main.log';
        @mkdir(dirname($path), 0770);
        
        
        $handler = new StreamHandler($path, Logger::WARNING);
        $handler->setFormatter(new LineFormatter(null, null, true));
        
        $log = new Logger('main');
        $log->pushHandler($handler);
        
        return $log;
    }
    
}