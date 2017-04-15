<?php
declare(strict_types=1);


namespace EMA\Domain\Foundation\Event;


use Carbon\Carbon;
use EMA\Domain\Foundation\VO\Identity;

interface DomainEvent
{
    public function __construct(Identity $aggregate_id, array $payload = []);
    
    public function getAggregateId(): Identity;
    
    public function getFiredAt(): Carbon;
    
    public function getPayload(): array;
    
    public function getVersion(): int;
}