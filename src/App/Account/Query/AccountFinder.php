<?php
declare(strict_types=1);


namespace EMA\App\Account\Query;


use Doctrine\Common\Collections\Collection;
use EMA\Domain\Foundation\VO\Identity;

interface AccountFinder
{
    public function all(): Collection;
    
    public function findBySocialId(string $social_provider_name, string $social_provider_id): array;
}