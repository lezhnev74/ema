<?php
declare(strict_types=1);

namespace EMA\Domain\Note\Commands\DeleteNote;

use EMA\Domain\Foundation\Command\Authorizer;
use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Model\Collection\NoteCollection;

class DeleteNoteAuthorizer extends Authorizer
{
    /** @var  NoteCollection */
    private $collection;
    
    /**
     * ModifyNoteAuthorizer constructor.
     *
     * @param NoteCollection $collection
     */
    public function __construct(NoteCollection $collection) { $this->collection = $collection; }
    
    
    /**
     * denied
     *
     *
     * @param Identity   $user_id
     * @param DeleteNote $command
     *
     * @return bool
     */
    public function denied(Identity $user_id=null, $command): bool
    {
        
        $note = $this->collection->findById($command->getId());
        
        return !$note->getOwnerId()->isEqual($user_id);
        
    }
}
