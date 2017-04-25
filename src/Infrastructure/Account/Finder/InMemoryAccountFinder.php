<?php
declare(strict_types=1);

namespace EMA\Infrastructure\Account\Finder;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Connection;
use EMA\App\Account\Model\Account\Account;
use EMA\App\Account\Model\Collection\AccountCollection;
use EMA\App\Account\Query\AccountFinder;
use EMA\Domain\Foundation\VO\Identity;

final class InMemoryAccountFinder implements AccountFinder
{
    /** @var  AccountCollection */
    private $collection;
    
    /**
     * InMemoryNoteFinder constructor.
     *
     * @param AccountCollection $collection
     */
    public function __construct(AccountCollection $collection) { $this->collection = $collection; }
    
    
    public function all(): Collection
    {
        return $this->collection->all()
                                ->map(function (Account $entry) {
                                    return [
                                        'id' => $entry->getId()->getAsString(),
                                        'social_provider_name' => $entry->getSocialProviderName(),
                                        'social_provider_id' => $entry->getSocialProviderId(),
                                    ];
                                });
    }
    
    public function findBySocialId(string $social_provider_name, string $social_provider_id): array
    {
        foreach ($this->all() as $item) {
            if ($item['social_provider_id'] == $social_provider_id && $item['social_provider_name'] == $social_provider_name) {
                return $item;
            }
        }
        
        return [];
    }
    
    
}