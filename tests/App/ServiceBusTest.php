<?php
declare(strict_types=1);

namespace EMA\Tests\App;

use EMA\Domain\Foundation\Command\AuthenticatedUserNotFound;
use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Commands\ModifyNote\ModifyNote;
use EMA\Domain\Note\Commands\PostNewNote\PostNewNote;
use EMA\Domain\Note\Model\Collection\NoteCollection;
use EMA\Domain\Note\Model\Note;
use EMA\Domain\Note\Model\VO\NoteText;
use EMA\Tests\BaseTest;
use Prooph\ServiceBus\Plugin\Guard\AuthorizationService;
use Prooph\ServiceBus\Plugin\Guard\UnauthorizedException;

final class ServiceBusTest extends BaseTest
{
    protected function setUp()
    {
        parent::setUp();
        
        $this->restartContainer();
    }
    
    function test_direct_routing()
    {
        $this->setAuthenticatedUser(new Identity());
        
        // 1. command
        $command = new PostNewNote(new NoteText("any"), new Identity(), new Identity());
        
        // 2. handle command directly (will involve authorizer)
        command_bus()->dispatch($command);
        
        // 3. assert note created
        $collection = container()->get(NoteCollection::class);
        $this->assertEquals(1, $collection->all()->count());
        
    }
    
    function test_it_throws_exception_on_authorization_deny()
    {
        $this->setAuthenticatedUser(new Identity());
        
        // make some note
        $note_id = new Identity();
        $note    = container()->get(NoteCollection::class)->save(
            new Note($note_id, new NoteText(""), new Identity())
        );
        
        // 1. command
        $command = new ModifyNote(new NoteText(""), $note_id);
        
        // 2. handle command directly
        try {
            command_bus()->dispatch($command);
            $this->fail('No authorization exception thrown');
        } catch (\Exception $e) {
            $this->assertEquals(UnauthorizedException::class, get_class($e->getPrevious()));
        }
        
    }
    
    function test_throw_exception_if_no_authenticated_user_set()
    {
        $this->expectException(AuthenticatedUserNotFound::class);
        
        container()->get(AuthorizationService::class);
        
    }
}