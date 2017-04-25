<?php
declare(strict_types=1);

namespace EMA\Tests\App\Account\Query\FindAccount;

use EMA\App\Account\Model\Account\Account;
use EMA\App\Account\Model\Collection\AccountCollection;
use EMA\App\Account\Query\FindAccount\FindAccount;
use EMA\App\Account\Query\FindAccount\FindAccountHandler;
use EMA\Domain\Foundation\VO\Identity;
use EMA\Tests\BaseTest;
use React\Promise\Deferred;

class FindAccountHandlerTest extends BaseTest
{
    
    function test_it_can_find_account_by_social_provider()
    {
        // seed account
        $social_provider    = "google";
        $social_provider_id = "123";
        container()->get(AccountCollection::class)->save(
            new Account(new Identity(), $social_provider, $social_provider_id)
        );
        
        // find it with query
        $query    = new FindAccount($social_provider, $social_provider_id);
        $deferred = new Deferred();
        $promise  = $deferred->promise();
        $result   = null;
        $promise->then(function ($answer) use (&$result) {
            $result = $answer;
        }, function (\Throwable $e) {
            $this->fail($e->getMessage());
        })->done();
        
        $handler = container()->get(FindAccountHandler::class);
        $handler->__invoke($query, $deferred);
        
        
        // assert good
        $this->assertEquals($social_provider, $result['social_provider_name']);
        $this->assertEquals($social_provider_id, $result['social_provider_id']);
    }
    
}
