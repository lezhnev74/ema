<?php
declare(strict_types = 1);

namespace EMA\Tests\App\Query\Note\AllNotes;

use EMA\App\Query\Note\AllNotes\AllNotes;
use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Model\Collection\NoteCollection;
use EMA\Domain\Note\Model\Note;
use EMA\Domain\Note\Model\VO\NoteText;
use EMA\Tests\BaseTest;
use PHPUnit\Framework\TestCase;
use Prooph\ServiceBus\Plugin\Guard\UnauthorizedException;
use Slim\Collection;

class AllNotesAuthorizerTest extends BaseTest {
    
    function test_it_works() {
        $this->restartContainer();
    
        $me = new Identity();
        $not_me = new Identity();
        $this->setAuthenticatedUser($me);
    
        $query  = new AllNotes($not_me);
        query_bus()->dispatch($query)->then(null,function(\Exception $e){
            $this->assertEquals(UnauthorizedException::class, get_class($e->getPrevious()));
        });
    }
    
}
