<?php
declare(strict_types=1);


namespace EMA\App\Note\Query;


use Doctrine\Common\Collections\Collection;
use EMA\Domain\Foundation\VO\Identity;

interface NoteFinder
{
    public function all(): Collection;
    
    public function search(string $query, Identity $ownerId): Collection;
    
    public function recent(int $count, Identity $ownerId): Collection;
}