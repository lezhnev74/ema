<?php
declare(strict_types = 1);

namespace EMA\App\Note\Query\SearchNotes;

use EMA\Domain\Foundation\Command\Command;
use EMA\Domain\Foundation\VO\Identity;

class SearchNotes implements Command {
    
    /** @var  Identity */
    private $owner_id;
    /** @var  string */
    private $query;
    
    /**
     * SearchNotes constructor.
     *
     * @param Identity $owner_id
     * @param string   $query
     */
    public function __construct(Identity $owner_id, string $query)
    {
        $this->owner_id = $owner_id;
        $this->query    = $query;
    }
    
    /**
     * @return Identity
     */
    public function getOwnerId(): Identity
    {
        return $this->owner_id;
    }
    
    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }
    
    
}
