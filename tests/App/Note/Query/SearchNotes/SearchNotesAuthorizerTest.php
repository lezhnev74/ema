<?php
declare(strict_types=1);

namespace EMA\Tests\App\Note\Query\SearchNotes;

use EMA\App\Note\Query\SearchNotes\SearchNotes;
use EMA\Domain\Foundation\VO\Identity;
use EMA\Tests\BaseTest;
use Prooph\ServiceBus\Plugin\Guard\UnauthorizedException;

class SearchNotesAuthorizerTest extends BaseTest
{
    
    function test_it_works()
    {
        $this->restartContainer();
        
        $me     = new Identity();
        $not_me = new Identity();
        $this->setAuthenticatedUser($me);
        
        $query = new SearchNotes($not_me, "");
        query_bus()->dispatch($query)->then(null, function (\Exception $e) {
            $this->assertEquals(UnauthorizedException::class, get_class($e->getPrevious()));
        });
    }
    
}
