<?php
declare(strict_types=1);

namespace EMA\App\Account\Model\Collection;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use EMA\App\Account\Model\Account\Account;

final class InMemoryAccountCollection implements AccountCollection
{
    private $items = [];
    
    public function save(Account $model): void
    {
        $this->items[$model->getId()->getAsString()] = $model;
    }
    
    public function all(): Collection
    {
        return new ArrayCollection($this->items);
    }
    
    /**
     * findFromSocialKey
     *
     *
     * @param string $social_provider
     * @param string $social_provider_id
     *
     * @throws AccountNotFound
     *
     * @return array
     */
    public function findFromSocialKey(string $social_provider, string $social_provider_id): array
    {
        foreach ($this->items as $item) {
            if ($item['social_provider_name'] == $social_provider && $item['social_provider_id'] = $social_provider_id) {
                return $item;
            }
        }
        throw new AccountNotFound();
    }
    
}