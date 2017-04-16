<?php
declare(strict_types=1);

namespace EMA\Tests\Services;

use EMA\Domain\Foundation\Event\DomainEventBase;
use EMA\Domain\Foundation\VO\Identity;
use Prooph\Common\Event\ActionEvent;
use Prooph\ServiceBus\MessageBus;
use Prooph\ServiceBus\Plugin\AbstractPlugin;

final class BusEventLogger extends AbstractPlugin
{
    private $recorded_events = [];
    
    
    public function __construct()
    {
        $this->wipe();
    }
    
    public function wipe()
    {
        $this->recorded_events = [];
    }
    
    public function attachToMessageBus(MessageBus $messageBus): void
    {
        $this->listenerHandlers[] = $messageBus->attach(
            MessageBus::EVENT_DISPATCH,
            [$this, 'onInitialize'],
            MessageBus::PRIORITY_INITIALIZE
        );
    }
    
    public function onInitialize(ActionEvent $actionEvent): void
    {
        $message                 = $actionEvent->getParam(MessageBus::EVENT_PARAM_MESSAGE);
        $this->recorded_events[] = $message;
    }
    
    /**
     * @return array
     */
    public function getRecordedEvents(): array
    {
        return $this->recorded_events;
    }
    
    public function assertHasEventForAggregateId(string $event_type, Identity $aggregate_id): bool
    {
        
        foreach ($this->getRecordedEvents() as $event) {
            if (is_a($event, DomainEventBase::class)) {
                if (get_class($event) == $event_type && $aggregate_id->isEqual($event->getAggregateId())) {
                    return true;
                }
            }
        }
        
        return false;
    }
}