<?php
declare(strict_types=1);

namespace EMA\Infrastructure\Note\Finder;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Connection;
use EMA\Domain\Foundation\VO\Identity;

final class DoctrineNoteFinder implements \EMA\App\Note\Query\NoteFinder
{
    /** @var  Connection */
    private $connection;
    
    /**
     * SqliteNoteCollection constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection) { $this->connection = $connection; }
    
    
    public function all(): Collection
    {
        $result     = $this->connection->fetchAll("select * from notes");
        $collection = new ArrayCollection($result);
        
        return $collection;
    }
    
    public function search(string $query, Identity $ownerId): Collection
    {
        $result = $this->connection->fetchAll(
            "select * from notes where owner_id=? AND `note_text` LIKE ?", [
                $ownerId->getAsString(),
                "%" . $query . "%",
            ]
        );
        log_info("sql search", [
            'sql' => "select * from notes where owner_id=? AND `note_text` LIKE ?",
            'binds' => [
                $ownerId->getAsString(),
                "%" . $query . "%",
            ],
        ]);
        $collection = new ArrayCollection($result);
        
        return $collection;
    }
    
    
}