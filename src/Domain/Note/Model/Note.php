<?php
declare(strict_types=1);

namespace EMA\Domain\Note\Model;

use Carbon\Carbon;
use EMA\Domain\Foundation\AggregateRoot;
use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Events\NoteDeleted;
use EMA\Domain\Note\Events\NoteModified;
use EMA\Domain\Note\Events\NotePosted;
use EMA\Domain\Note\Model\VO\NoteText;

final class Note extends AggregateRoot
{
    /** @var  NoteText */
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
     * @param NoteText $text
     * @param Identity $owner_id
     */
    public function __construct(
        Identity $id,
        NoteText $text,
        Identity $owner_id,
        Carbon $modified_at = null,
        Carbon $posted_at = null
    ) {
        $this->id          = $id;
        $this->text        = $text;
        $this->posted_at   = $posted_at ?? Carbon::now();
        $this->modified_at = $modified_at ?? Carbon::now();
        $this->owner_id    = $owner_id;
    }
    
    /**
     * make a note
     *
     *
     * @param Identity $id
     * @param NoteText $text
     * @param Identity $owner_id
     *
     * @return Note
     */
    static public function make(Identity $id, NoteText $text, Identity $owner_id): self
    {
        $model = new static($id, $text, $owner_id);
        $model->apply(new NotePosted($id));
        
        return $model;
    }
    
    
    /**
     * modify
     *
     *
     * @param NoteText $text
     *
     * @return void
     */
    public function modify(NoteText $text): void
    {
        $this->text        = $text;
        $this->modified_at = Carbon::now();
        
        $this->apply(new NoteModified($this->getId()));
    }
    
    /**
     * Delete
     *
     *
     * @return void
     */
    public function delete(): void
    {
        $this->apply(new NoteDeleted($this->getId()));
    }
    
    /**
     * @return NoteText
     */
    public function getText(): NoteText
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
     * @return Carbon|null
     */
    public function getModifiedAt()
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