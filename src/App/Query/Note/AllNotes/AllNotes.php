<?php
declare(strict_types=1);

namespace EMA\App\Query\Note\AllNotes;

use EMA\Domain\Foundation\Command\Command;
use EMA\Domain\Foundation\VO\Identity;

class AllNotes implements Command
{
    /** @var  Identity */
    private $owner_id;
    
    /**
     * AllNotes constructor.
     *
     * @param Identity $owner_id
     */
    public function __construct(Identity $owner_id) { $this->owner_id = $owner_id; }
    
    /**
     * @return Identity
     */
    public function getOwnerId(): Identity
    {
        return $this->owner_id;
    }
    
}
