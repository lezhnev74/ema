<?php
declare(strict_types=1);

namespace EMA\Domain\Note\Commands\DeleteNote;

use EMA\Domain\Note\Model\Collection\NoteCollection;

class DeleteNoteHandler
{
    
    private $collection;
    
    /**
     * PostNewNoteHandler constructor.
     *
     * @param $collection
     */
    public function __construct(NoteCollection $collection) { $this->collection = $collection; }
    
    
    function __invoke(DeleteNote $command): void
    {
        $note = $this->collection->findById($command->getId());
        $note->delete();
        $this->collection->delete($command->getId());
    }
    
}
