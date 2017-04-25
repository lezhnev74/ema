<?php
declare(strict_types=1);

namespace EMA\App\Account\Query\FindAccount;

use EMA\App\Account\Query\AccountFinder;
use React\Promise\Deferred;

class FindAccountHandler
{
    /** @var  AccountFinder */
    private $finder;
    
    /**
     * FindAccountHandler constructor.
     *
     * @param AccountFinder $finder
     */
    public function __construct(AccountFinder $finder) { $this->finder = $finder; }
    
    
    function __invoke(FindAccount $query, Deferred $deferred = null)
    {
        
        $result = $this->finder->findBySocialId($query->getSocialProviderName(), $query->getSocialProviderId());
        if (null === $deferred) {
            return $result;
        }
        
        $deferred->resolve($result);
    }
    
    
}
