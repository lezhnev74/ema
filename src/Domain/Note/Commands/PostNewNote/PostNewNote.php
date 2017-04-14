<?php
declare(strict_types=1);

namespace EMA\Domain\Note\Commands\PostNewNote;

use Assert\Assert;
use \EMA\Domain\Foundation\Command\Command;
use EMA\Domain\Foundation\VO\Identity;


class PostNewNote implements Command
{
    
    /** @var  string */
    private $text;
    /** @var  Identity */
    private $id;
    /** @var  Identity */
    private $owner_id;
    
    /**
     * PostNewNote constructor.
     *
     * @param string   $text
     * @param Identity $id
     * @param Identity $owner_id
     */
    public function __construct(string $text, Identity $id, Identity $owner_id)
    {
        $this->text     = $text;
        $this->id       = $id;
        $this->owner_id = $owner_id;
        
        Assert::that($text)->maxLength(10000);
    }
    
    /**
     * @return string
     */
    public function getText(): string
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
    
    /**
     * @return Identity
     */
    public function getOwnerId(): Identity
    {
        return $this->owner_id;
    }
    
    
}
