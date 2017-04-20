<?php
declare(strict_types=1);

namespace EMA\Infrastructure\Note\Finder;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Connection;
use EMA\Domain\Note\Model\Collection\NoteCollection;
use EMA\Domain\Note\Model\Note;

final class InMemoryNoteFinder implements \EMA\App\Query\Note\NoteFinder
{
    /** @var  NoteCollection */
    private $collection;
    
    /**
     * InMemoryNoteFinder constructor.
     *
     * @param NoteCollection $collection
     */
    public function __construct(NoteCollection $collection) { $this->collection = $collection; }
    
    
    public function all(): Collection
    {
        return $this->collection->all()
                                ->map(function (Note $entry) {
                                    return [
                                        'id' => $entry->getId()->getAsString(),
                                        'owner_id' => $entry->getOwnerId()->getAsString(),
                                        'text' => $entry->getText()->getText(),
                                    ];
                                });
    }
}