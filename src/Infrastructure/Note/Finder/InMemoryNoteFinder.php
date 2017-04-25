<?php
declare(strict_types=1);

namespace EMA\Infrastructure\Note\Finder;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Connection;
use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Model\Collection\NoteCollection;
use EMA\Domain\Note\Model\Note;

final class InMemoryNoteFinder implements \EMA\App\Note\Query\NoteFinder
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
    
    public function search(string $query, Identity $owner_id): Collection
    {
        return $this->collection->all()
                                ->map(function (Note $entry) {
                                    return [
                                        'id' => $entry->getId()->getAsString(),
                                        'owner_id' => $entry->getOwnerId()->getAsString(),
                                        'text' => $entry->getText()->getText(),
                                    ];
                                })
                                ->filter(function (array $entry) use ($owner_id) {
                                    return $entry['owner_id'] == $owner_id->getAsString();
                                })
                                ->filter(function (array $entry) use ($query) {
                                    return preg_match("#" . $query . "#i", $entry['text']);
                                });
    }
}