<?php
declare(strict_types=1);

namespace EMA\Infrastructure\Note\Collection;

use Carbon\Carbon;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Connection;
use EMA\Domain\Foundation\Exception\ModelNotFound;
use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Model\Collection\NoteCollection;
use EMA\Domain\Note\Model\Note;
use EMA\Domain\Note\Model\VO\NoteText;

final class DoctrineNoteCollection implements NoteCollection
{
    /** @var  Connection */
    private $connection;
    
    /**
     * SqliteNoteCollection constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection) { $this->connection = $connection; }
    
    
    public function all($page = 1, $on_page = 100): Collection
    {
        $result     = $this->connection->fetchAll("select * from notes LIMIT " . $on_page . " OFFSET " . ($on_page * ($page - 1)));
        $collection = new ArrayCollection($result);
        $collection = $collection->map(\Closure::fromCallable([$this, 'toObject']));
        
        return $collection;
    }
    
    public function findById(Identity $id): Note
    {
        $sql     = "select * from notes where id=:id";
        $results = $this->connection->fetchAll($sql, [":id" => $id->getAsString()]);
        
        if (!count($results)) {
            throw new ModelNotFound();
        }
        
        return $this->toObject($results[0]);
    }
    
    public function save(Note $note): void
    {
        // Possibly that record with this ID exists
        try {
            $this->findById($note->getId());
            
            $sql       = "UPDATE notes SET note_text=:text";
            $statement = $this->connection->prepare($sql);
            $statement->bindValue(1, $note->getText()->getText());
        } catch (ModelNotFound $e) {
            $sql       = "INSERT into notes(id, note_text, owner_id, posted_at, modified_at)
                                    VALUES(:id, :text, :owner_id, :posted_at, :modified_at)";
            $statement = $this->connection->prepare($sql);
            $statement->bindValue(1, $note->getId()->getAsString());
            $statement->bindValue(2, $note->getText()->getText());
            $statement->bindValue(3, $note->getOwnerId()->getAsString());
            $statement->bindValue(4, $note->getPostedAt()->timestamp);
            $statement->bindValue(5, $note->getModifiedAt()->timestamp);
        }
        
        
        $statement->execute();
    }
    
    public function delete(Identity $id): void
    {
        $sql       = "DELETE from notes where id=:id";
        $statement = $this->connection->prepare($sql);
        $statement->bindValue(":id", $id->getAsString());
        $statement->execute();
    }
    
    /**
     * toObject
     * Transform row into Aggregate root
     *
     * @param array $item
     *
     * @return Note
     */
    private function toObject(array $item): Note
    {
        return new Note(
            new Identity($item['id']),
            new NoteText($item['note_text']),
            new Identity($item['owner_id']),
            isset($item['modified_at']) ? Carbon::createFromTimestamp($item['modified_at']) : null,
            Carbon::createFromTimestamp($item['posted_at'])
        );
    }
}