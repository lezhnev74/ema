<?php
declare(strict_types=1);

namespace EMA\Tests\App\Query\Note\SearchNotes;

use EMA\App\Query\Note\SearchNotes\SearchNotes;
use EMA\Domain\Foundation\VO\Identity;
use EMA\Tests\BaseTest;
use PHPUnit\Framework\TestCase;
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
