<?php
declare(strict_types=1);

namespace EMA\Domain\Note\Model;

use Carbon\Carbon;
use EMA\Domain\Foundation\AggregateRoot;
use EMA\Domain\Foundation\VO\Identity;

final class Note extends AggregateRoot
{
    /** @var  string */
    private $text;
    /** @var  Carbon */
    private $posted_at;
    /** @var  Carbon */
    private $modified_at;
    /** @var  Identity */
    private $owner_id;
    
    /**
     * Note constructor.
     *
     * @param Identity $id
     * @param string   $text
     * @param Identity $owner_id
     */
    private function __construct(Identity $id, string $text, Identity $owner_id)
    {
        $this->id          = $id;
        $this->text        = $text;
        $this->posted_at   = Carbon::now();
        $this->modified_at = null;
        $this->owner_id    = $owner_id;
    }
    
    /**
     * make
     *
     *
     * @param Identity $id
     * @param string   $text
     * @param Identity $owner_id
     *
     * @return Note
     */
    static public function make(Identity $id, string $text, Identity $owner_id): self
    {
        return new static($id, $text, $owner_id);
    }
    
    public function modify(Identity $id, string $text): void
    {
        $this->text        = $text;
        $this->modified_at = Carbon::now();
    }
    
    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
    
    /**
     * @return Carbon
     */
    public function getPostedAt(): Carbon
    {
        return $this->posted_at;
    }
    
    /**
     * @return Carbon
     */
    public function getModifiedAt(): Carbon
    {
        return $this->modified_at;
    }
    
    /**
     * @return Identity
     */
    public function getOwnerId(): Identity
    {
        return $this->owner_id;
    }
    
    
}