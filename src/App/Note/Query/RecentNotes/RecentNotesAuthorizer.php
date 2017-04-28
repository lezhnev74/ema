<?php
declare(strict_types = 1);

namespace EMA\App\Note\Query\RecentNotes;

use EMA\Domain\Foundation\Command\Authorizer;
use EMA\Domain\Foundation\VO\Identity;

class RecentNotesAuthorizer  extends  Authorizer {
    public function denied(Identity $user_id = null, $command): bool
    {
        return false;
    }
    
    
}
