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
    
    /**
     * DomainEvent constructor.
     *
     * @param Identity $aggregate_id
     * @param array    $payload
     */
    public function __construct(Identity $aggregate_id, array $payload = [])
    {
        $this->aggregate_id = $aggregate_id;
        $this->payload      = $payload;
        $this->fired_at     = Carbon::now();
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
    
}