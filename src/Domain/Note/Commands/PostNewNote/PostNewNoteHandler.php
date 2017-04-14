<?php
declare(strict_types=1);

namespace EMA\Domain\Note\Commands\PostNewNote;

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
    
    
    function __invoke(PostNewNote $command): void
    {
        $note = Note::make($command->getId(), $command->getText(), $command->getOwnerId());
        $this->collection->save($note);
    }
    
}
