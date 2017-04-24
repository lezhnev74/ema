<?php
declare(strict_types=1);

namespace EMA\Domain\Note\Commands\PostNewNote;

use Assert\Assert;
use EMA\Domain\Note\Model\Collection\NoteCollection;
use EMA\Domain\Note\Model\Note;

class PostNewNoteHandler
{
    private $collection;
    
    /**
     * PostNewNoteHandler constructor.
     *
     * @param $collection
     */
    public function __construct(NoteCollection $collection) { $this->collection = $collection; }
    
    
    public function __invoke(PostNewNote $command): void
    {
        Assert::that($command->getText()->getText())->minLength(1, "Sorry, unable to add empty note");
        
        $note = Note::make($command->getId(), $command->getText(), $command->getOwnerId());
        $this->collection->save($note);
        
        // Fire domain events
        array_map([event_bus(), 'dispatch'], $note->pullDomainEvents());
    }
    
}
