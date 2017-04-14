<?php
declare(strict_types = 1);

namespace EMA\Domain\Note\Commands\ModifyNote;

use Assert\Assert;
use EMA\Domain\Foundation\Command\Command;
use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Model\VO\NoteText;

class ModifyNote implements Command {
    
    /** @var  NoteText */
    private $text;
    /** @var  Identity */
    private $id;
    
    /**
     * PostNewNote constructor.
     *
     * @param NoteText   $text
     * @param Identity $id
     */
    public function __construct(NoteText $text, Identity $id)
    {
        $this->text     = $text;
        $this->id       = $id;
    }
    
    /**
     * @return NoteText
     */
    public function getText(): NoteText
    {
        return $this->text;
    }
    
    /**
     * @return Identity
     */
    public function getId(): Identity
    {
        return $this->id;
    }
    
    
}
