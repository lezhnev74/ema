<?php
declare(strict_types=1);

namespace EMA\Infrastructure\Note\Finder;

use Carbon\Carbon;
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
        return $this->mapObjects($this->collection->all());
    }
    
    public function search(string $query, Identity $owner_id): Collection
    {
        return $this->mapObjects($this->collection->all())
                    ->filter(function (array $entry) use ($owner_id) {
                        return $entry['owner_id'] == $owner_id->getAsString();
                    })
                    ->filter(function (array $entry) use ($query) {
                        return preg_match("#" . $query . "#i", $entry['text']);
                    });
    }
    
    public function recent(int $count, Identity $ownerId): Collection
    {
        $array = $this->mapObjects($this->collection->all())
                      ->filter(function (array $entry) use ($ownerId) {
                          return $entry['owner_id'] == $ownerId->getAsString();
                      })
                      ->toArray();
        usort($array, function ($a, $b) {
            return Carbon::parse($b['posted_at'])->gt(Carbon::parse($a['posted_at']));
        });
        usort($array, function ($a, $b) {
            return Carbon::parse($b['modified_at'])->gt(Carbon::parse($a['modified_at']));
        });
        
        return new ArrayCollection(array_slice($array, 0, $count));
    }
    
    private function mapObjects(Collection $col): Collection
    {
        return $col->map(function (Note $entry) {
            return [
                'id' => $entry->getId()->getAsString(),
                'owner_id' => $entry->getOwnerId()->getAsString(),
                'text' => $entry->getText()->getText(),
                'posted_at' => $entry->getPostedAt()->toIso8601String(),
                'modified_at' => $entry->getModifiedAt() ? $entry->getModifiedAt()->toIso8601String() : null,
            ];
        });
    }
    
    
}