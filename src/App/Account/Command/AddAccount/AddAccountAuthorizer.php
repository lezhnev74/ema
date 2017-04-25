<?php
declare(strict_types=1);

namespace EMA\App\Account\Command\AddAccount;

use EMA\Domain\Foundation\Command\Authorizer;
use EMA\Domain\Foundation\VO\Identity;

class AddAccountAuthorizer extends Authorizer
{
    public function denied(Identity $user_id=null, $command): bool
    {
        return false;
    }
    
}
