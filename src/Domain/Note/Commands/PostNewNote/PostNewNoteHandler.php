<?php
declare(strict_types=1);

namespace EMA\Domain\Note\Commands\PostNewNote;

use EMA\Domain\Note\Model\Collection\NoteCollection;

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
    
    }
    
}
