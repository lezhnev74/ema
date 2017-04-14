<?php
declare(strict_types=1);


namespace EMA\Domain\Foundation\Command;


abstract class Authorizer
{
    public function denied(): bool
    {
        return true; // default, whould be reloaded in actual class
    }
}