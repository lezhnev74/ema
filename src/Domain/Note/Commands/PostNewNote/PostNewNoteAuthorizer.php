<?php
declare(strict_types=1);

namespace EMA\Domain\Note\Commands\PostNewNote;

use EMA\Domain\Foundation\Command\Authorizer;
use EMA\Domain\Foundation\VO\Identity;

class PostNewNoteAuthorizer extends Authorizer
{
    public function denied(Identity $user_id=null, $command): bool
    {
        return false;
    }
    
    
}
