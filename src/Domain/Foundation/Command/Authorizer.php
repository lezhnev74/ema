<?php
declare(strict_types=1);


namespace EMA\Domain\Foundation\Command;


use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Commands\DeleteNote\DeleteNote;

abstract class Authorizer
{
    
    /**
     * denied
     *
     *
     * @param Identity|null $user_id who sent this command (null means guest)
     * @param mixed         $command
     *
     * @return bool
     */
    public function denied(Identity $user_id = null, $command): bool
    {
        return true; // default, whould be reloaded in actual class
    }
}
