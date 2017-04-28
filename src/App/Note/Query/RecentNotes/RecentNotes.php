<?php
declare(strict_types=1);

namespace EMA\App\Note\Query\RecentNotes;

use EMA\Domain\Foundation\Command\Command;
use EMA\Domain\Foundation\VO\Identity;

class RecentNotes implements Command
{
    /** @var  Identity */
    private $owner_id;
    /** @var  int */
    private $count;
    
    /**
     * RecentNotes constructor.
     *
     * @param Identity $owner_id
     * @param int      $count
     */
    public function __construct(Identity $owner_id, $count)
    {
        $this->owner_id = $owner_id;
        $this->count    = $count;
    }
    
    /**
     * @return Identity
     */
    public function getOwnerId(): Identity
    {
        return $this->owner_id;
    }
    
    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }
    
    
}
