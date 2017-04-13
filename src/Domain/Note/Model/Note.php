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
    
    /**
     * Note constructor.
     *
     * @param Identity $id
     * @param string   $text
     */
    public function __construct(Identity $id, $text)
    {
        $this->id          = $id;
        $this->text        = $text;
        $this->posted_at   = Carbon::now();
        $this->modified_at = null;
    }
    
    /**
     * post
     *
     *
     * @param Identity $id
     * @param string   $text
     *
     * @return Note
     */
    static public function post(Identity $id, string $text): self
    {
        return new Note($id, $text);
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
    
    
}