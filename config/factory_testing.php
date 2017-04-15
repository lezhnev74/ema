<?php


use function DI\object;
use EMA\Domain\Note\Model\Collection\InMemoryNoteCollection;
use EMA\Domain\Note\Model\Collection\NoteCollection;

return [
    
    //
    // APP
    //
    
    //Prooph\ServiceBus\Plugin\Guard\AuthorizationService::class => object(\EMA\App\Authorization\AuthorizationService::class),
    
    //
    // DOMAIN
    //
    
    // Note
    NoteCollection::class => object(InMemoryNoteCollection::class),


];

