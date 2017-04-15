<?php
declare(strict_types=1);

namespace EMA\Domain\Foundation\Event;

use Carbon\Carbon;
use EMA\Domain\Foundation\VO\Identity;

abstract class DomainEventBase implements DomainEvent
{
    /** @var  Identity */
    private $aggregate_id;
    /** @var Carbon */
    private $fired_at;
    /** @var  array */
    private $payload;
    /** @var  int */
    private $version;
    
    /**
     * DomainEvent constructor.
     *
     * @param Identity $aggregate_id
     * @param array    $payload
     */
    public function __construct(Identity $aggregate_id, array $payload = [], $version = 1)
    {
        $this->aggregate_id = $aggregate_id;
        $this->payload      = $payload;
        $this->fired_at     = Carbon::now();
        $this->version      = $version;
    }
    
    /**
     * @return Identity
     */
    public function getAggregateId(): Identity
    {
        return $this->aggregate_id;
    }
    
    /**
     * @return mixed
     */
    public function getFiredAt(): Carbon
    {
        return $this->fired_at;
    }
    
    /**
     * @return array
     */
    public function getPayload(): array
    {
        return $this->payload;
    }
    
    public function getVersion(): int
    {
        return $this->version;
    }
    
}