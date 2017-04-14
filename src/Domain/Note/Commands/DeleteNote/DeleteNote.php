<?php
declare(strict_types = 1);

namespace EMA\Domain\Note\Commands\DeleteNote;

use EMA\Domain\Foundation\Command\Command;
use EMA\Domain\Foundation\VO\Identity;

class DeleteNote implements Command {
    /** @var  Identity */
    private $id;
    
    /**
     * DeleteNote constructor.
     *
     * @param Identity $id
     */
    public function __construct(Identity $id) { $this->id = $id; }
    
    /**
     * @return Identity
     */
    public function getId(): Identity
    {
        return $this->id;
    }
    
    
    
}
