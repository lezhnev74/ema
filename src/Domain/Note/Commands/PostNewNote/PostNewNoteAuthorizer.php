<?php
declare(strict_types=1);

namespace EMA\Domain\Note\Commands\PostNewNote;

use EMA\Domain\Foundation\Command\Authorizer;

class PostNewNoteAuthorizer implements Authorizer
{
    
    public function denied(): bool
    {
        return false; // any user can post notes
    }
    
}
