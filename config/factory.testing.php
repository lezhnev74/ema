<?php


use EMA\Domain\Note\Model\Collection\InMemoryNoteCollection;
use EMA\Domain\Note\Model\Collection\NoteCollection;

return [
    "container_factories" => [
        
        // Note
        NoteCollection::class => InMemoryNoteCollection::class,
    
    ],
];

