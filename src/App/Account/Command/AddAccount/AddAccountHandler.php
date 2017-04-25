<?php
declare(strict_types=1);

namespace EMA\App\Account\Command\AddAccount;

use Assert\Assert;
use EMA\App\Account\Model\Account\Account;
use EMA\App\Account\Model\Collection\AccountCollection;
use EMA\App\Account\Model\Collection\AccountNotFound;
use EMA\Domain\Foundation\VO\Identity;

class AddAccountHandler
{
    
    /** @var  AccountCollection */
    private $collection;
    
    /**
     * AddAccountHandler constructor.
     *
     * @param AccountCollection $collection
     */
    public function __construct(AccountCollection $collection) { $this->collection = $collection; }
    
    
    function __invoke(AddAccount $command)
    {
        try {
            $existing_account = $this->collection->findFromSocialKey(
                $command->getSocialProviderName(),
                $command->getSocialProviderId()
            );
        } catch (AccountNotFound $e) {
            $this->collection->save(
                new Account(
                    new Identity(),
                    $command->getSocialProviderName(),
                    $command->getSocialProviderId()
                )
            );
        }
    }
    
}
