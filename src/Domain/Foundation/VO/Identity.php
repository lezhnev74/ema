<?php
declare(strict_types=1);

namespace EMA\Domain\Foundation\VO;

use Ramsey\Uuid\Uuid;

final class Identity
{
    /** @var Uuid */
    private $uuid;
    
    /**
     * Identity constructor.
     *
     * @param string $uuid
     */
    public function __construct(string $uuid = null)
    {
        if ($uuid) {
            $this->uuid = Uuid::fromString($uuid);
        } else {
            $this->uuid = Uuid::uuid1();
        }
    }
    
    /**
     * @return string
     */
    public function getAsString(): string
    {
        return $this->uuid->toString();
    }
    
    
    /**
     * isEqual
     *
     *
     * @param Identity $id
     *
     * @return bool
     */
    public function isEqual(Identity $id): bool
    {
        return $id->getAsString() === $this->getAsString();
    }
}