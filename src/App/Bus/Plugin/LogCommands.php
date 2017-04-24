<?php
declare(strict_types=1);

namespace EMA\App\Bus\Plugin;

use Prooph\Common\Event\ActionEvent;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\AbstractPlugin;

final class LogCommands extends AbstractPlugin
{
    public function attachToMessageBus(MessageBus $messageBus): void
    {
        
        $messageBus->attach(MessageBus::EVENT_FINALIZE, function (ActionEvent $actionEvent) {
            
            $name    = $actionEvent->getParam(MessageBus::EVENT_PARAM_MESSAGE_NAME);
            $message = $actionEvent->getParam(MessageBus::EVENT_PARAM_MESSAGE);
            
            log_info("Command: " . $name, [
                'command' => var_export($message, true),
            ]);
            
        }, MessageBus::PRIORITY_INITIALIZE);
        
    }
    
}