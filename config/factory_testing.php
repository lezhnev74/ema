<?php


use function DI\object;
use EMA\Domain\Note\Model\Collection\InMemoryNoteCollection;
use EMA\Domain\Note\Model\Collection\NoteCollection;

return [
    
    
    // Note
    NoteCollection::class => object(InMemoryNoteCollection::class),


];

