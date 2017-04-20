<?php
declare(strict_types=1);

namespace EMA\Infrastructure\Note\Finder;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Connection;

final class DoctrineNoteFinder implements \EMA\App\Query\Note\NoteFinder
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
}