<?php
declare(strict_types=1);

namespace EMA\Infrastructure\Account\Finder;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Connection;
use EMA\App\Account\Model\Account\Account;
use EMA\App\Account\Model\Collection\AccountCollection;
use EMA\App\Account\Query\AccountFinder;
use EMA\Domain\Foundation\VO\Identity;

final class DoctrineAccountFinder implements AccountFinder
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
        $result     = $this->connection->fetchAll("select * from accounts");
        $collection = new ArrayCollection($result);
        
        return $collection;
    }
    
    public function findBySocialId(string $social_provider_name, string $social_provider_id): array
    {
        $result = $this->connection->fetchAssoc(
            "select * from accounts where social_provider_id=? AND social_provider_name=?", [
                $social_provider_id,
                $social_provider_name,
            ]
        );
        
        return $result;
        
    }
    
    
}