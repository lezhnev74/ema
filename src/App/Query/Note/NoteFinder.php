<?php
declare(strict_types=1);


namespace EMA\App\Query\Note;


use Doctrine\Common\Collections\Collection;

interface NoteFinder
{
    public function all(): Collection;
}