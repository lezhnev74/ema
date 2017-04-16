<?php
declare(strict_types=1);

namespace EMA\Tests\Domain\Note\Commands\DeleteNote;

use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Commands\DeleteNote\DeleteNote;
use EMA\Domain\Note\Commands\DeleteNote\DeleteNoteHandler;
use EMA\Domain\Note\Events\NoteDeleted;
use EMA\Domain\Note\Model\Collection\NoteCollection;
use EMA\Domain\Note\Model\Note;
use EMA\Domain\Note\Model\VO\NoteText;
use EMA\Tests\BaseTest;
use Faker\Factory;

class DeleteNoteHandlerTest extends BaseTest
{
    protected function setUp()
    {
        parent::setUp();
        container()->get(NoteCollection::class)->wipe();
        
        $this->restartContainer();
        $this->setAuthorizationAs(true);
    }
    
    
    function test_it_deletes_note()
    {
        
        $faker    = Factory::create();
        $id       = new Identity();
        $owner_id = new Identity();
        $text     = new NoteText($faker->text);
        
        $collection = container()->get(NoteCollection::class);
        
        $note = Note::make($id, $text, $owner_id);
        $collection->save($note);
        $this->assertEquals(1, $collection->all()->count());
        
        $handler = container()->get(DeleteNoteHandler::class);
        $handler->__invoke(new DeleteNote($id));
        
        $this->assertEquals(0, $collection->all()->count());
        
    }
    
    function test_it_fires_event()
    {
        $faker    = Factory::create();
        $id       = new Identity();
        $owner_id = new Identity();
        $text     = new NoteText($faker->text);
        $logger   = $this->getEventBusLogger();
        
        // create note
        $collection = container()->get(NoteCollection::class);
        $note       = Note::make($id, $text, $owner_id);
        $collection->save($note);
        $this->assertEquals(1, $collection->all()->count());
        
        // modify note
        command_bus()->dispatch(new DeleteNote($id));
        
        $this->assertTrue($logger->assertHasEventForAggregateId(NoteDeleted::class, $id));
    }
    
}
