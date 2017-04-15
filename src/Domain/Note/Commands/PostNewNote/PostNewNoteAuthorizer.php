<?php
declare(strict_types=1);

namespace EMA\Domain\Note\Commands\PostNewNote;

use EMA\Domain\Foundation\Command\Authorizer;

class PostNewNoteAuthorizer extends Authorizer
{
    public function denied(): bool
    {
        return false; // any account can post new notes
    }
    
}
