<?php
declare(strict_types=1);

namespace EMA\Infrastructure\Account\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Connection;
use EMA\App\Account\Model\Account\Account;
use EMA\App\Account\Model\Collection\AccountCollection;
use EMA\Domain\Foundation\Exception\ModelNotFound;
use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Model\Collection\NoteCollection;
use EMA\Domain\Note\Model\Note;
use EMA\Domain\Note\Model\VO\NoteText;

final class DoctrineAccountCollection implements AccountCollection
{
    /** @var  Connection */
    private $connection;
    
    /**
     * SqliteNoteCollection constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection) { $this->connection = $connection; }
    
    
    public function save(Account $account): void
    {
        // Possibly that record with this ID exists
        
        
        $sql     = "select * from accounts where id=?";
        $results = $this->connection->fetchAll($sql, [$account->getId()->getAsString()]);
        
        if (count($results)) {
            $sql       = "UPDATE accounts SET social_provider_id=? and social_provider_name=?";
            $statement = $this->connection->prepare($sql);
            $statement->bindValue(1, $account->getSocialProviderId());
            $statement->bindValue(2, $account->getSocialProviderName());
        } else {
            $sql       = "INSERT INTO accounts(id, social_provider_name, social_provider_id) VALUES(?,?,?)";
            $statement = $this->connection->prepare($sql);
            $statement->bindValue(1, $account->getId()->getAsString());
            $statement->bindValue(2, $account->getSocialProviderName());
            $statement->bindValue(3, $account->getSocialProviderId());
            
        }
        
        $statement->execute();
    }
    
}