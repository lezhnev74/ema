<?php
declare(strict_types=1);

namespace EMA\Tests\App;

use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Commands\PostNewNote\PostNewNote;
use EMA\Domain\Note\Commands\PostNewNote\PostNewNoteAuthorizer;
use EMA\Domain\Note\Model\Collection\NoteCollection;
use EMA\Domain\Note\Model\VO\NoteText;
use EMA\Tests\BaseTest;
use Prooph\ServiceBus\Plugin\Guard\AuthorizationService;
use Prooph\ServiceBus\Plugin\Guard\UnauthorizedException;

final class ServiceBusTest extends BaseTest
{
    protected function setUp()
    {
        parent::setUp();
        container()->get(NoteCollection::class)->wipe();
    }
    
    function test_direct_routing()
    {
        // 1. command
        $command = new PostNewNote(new NoteText(""), new Identity(), new Identity());
        
        // 2. handle command directly (will involve authorizer)
        command_bus()->dispatch($command);
        
        // 3. assert note created
        $collection = container()->get(NoteCollection::class);
        $this->assertEquals(1, $collection->all()->count());
        
    }
    
    function test_it_throws_exception_on_authorization_deny()
    {
        $this->restartContainer();
        $this->setAuthorizationAs(false);
        
        // 1. command
        $command = new PostNewNote(new NoteText(""), new Identity(), new Identity());
        
        // 2. handle command directly
        try {
            command_bus()->dispatch($command);
            $this->fail('No authorization exception thrown');
        } catch (\Exception $e) {
            $this->assertEquals(UnauthorizedException::class, get_class($e->getPrevious()));
        }
        
    }
}