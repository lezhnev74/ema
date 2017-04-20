<?php


use function DI\object;
use EMA\App\Query\Note\NoteFinder;
use EMA\Domain\Note\Model\Collection\InMemoryNoteCollection;
use EMA\Domain\Note\Model\Collection\NoteCollection;
use EMA\Infrastructure\Note\Finder\InMemoryNoteFinder;

return [
    
    //
    // APP
    //
    
    NoteFinder::class => object(InMemoryNoteFinder::class),
    
    //Prooph\ServiceBus\Plugin\Guard\AuthorizationService::class => object(\EMA\App\Authorization\AuthorizationService::class),
    
    //
    // DOMAIN
    //
    
    // Note
    NoteCollection::class => object(InMemoryNoteCollection::class),


];

