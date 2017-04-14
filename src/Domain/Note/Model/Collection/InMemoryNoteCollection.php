<?php
declare(strict_types=1);

namespace EMA\Domain\Note\Model\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Model\Note;

final class InMemoryNoteCollection implements NoteCollection
{
    /** @var  Collection */
    private $collection;
    
    /**
     * InMemoryNoteCollection constructor.
     *
     * @param Collection $collection
     */
    public function __construct()
    {
        $this->collection = new ArrayCollection();
    }
    
    public function all($page = 1, $on_page = 100): Collection
    {
        return new ArrayCollection($this->collection->slice($on_page * ($page - 1), $on_page));
    }
    
    public function findById(Identity $id): Note
    {
        if (!$this->collection->contains($id->getAsString())) {
            throw new \Exception();
        }
        
        return $this->collection->get($id->getAsString());
    }
    
    public function save(Note $note): void
    {
        $this->collection[$note->getId()->getAsString()] = $note;
    }
    
    public function delete(Identity $id): void
    {
        $this->collection->remove($id->getAsString());
    }
    
}