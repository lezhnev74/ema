<?php
declare(strict_types=1);

namespace EMA\Domain\Note\Commands\ModifyNote;

use EMA\Domain\Note\Model\Collection\NoteCollection;

class ModifyNoteHandler
{
    
    private $collection;
    
    /**
     * PostNewNoteHandler constructor.
     *
     * @param $collection
     */
    public function __construct(NoteCollection $collection) { $this->collection = $collection; }
    
    
    function __invoke(ModifyNote $command): void
    {
        $note = $this->collection->findById($command->getId());
        
        $note->modify($command->getText());
        
        $this->collection->save($note);
    
        // Fire domain events
        array_map([event_bus(), 'dispatch'], $note->pullDomainEvents());
    }
    
}
