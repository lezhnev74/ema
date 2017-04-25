<?php


use function DI\object;
use EMA\App\Account\Model\Collection\AccountCollection;
use EMA\App\Account\Model\Collection\InMemoryAccountCollection;
use EMA\App\Account\Query\AccountFinder;
use EMA\App\Note\Query\NoteFinder;
use EMA\Domain\Note\Model\Collection\InMemoryNoteCollection;
use EMA\Domain\Note\Model\Collection\NoteCollection;
use EMA\Infrastructure\Account\Finder\InMemoryAccountFinder;
use EMA\Infrastructure\Note\Finder\InMemoryNoteFinder;

return [
    
    //
    // APP
    //
    
    NoteFinder::class => object(InMemoryNoteFinder::class),
    AccountCollection::class => object(InMemoryAccountCollection::class),
    AccountFinder::class => object(InMemoryAccountFinder::class),
    
    //Prooph\ServiceBus\Plugin\Guard\AuthorizationService::class => object(\EMA\App\Authorization\AuthorizationService::class),
    
    //
    // DOMAIN
    //
    
    // Note
    NoteCollection::class => object(InMemoryNoteCollection::class),


];

