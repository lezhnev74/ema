<?php
declare(strict_types=1);


namespace EMA\Domain\Foundation\Command;


interface Authorizer
{
    public function denied(): bool;
}