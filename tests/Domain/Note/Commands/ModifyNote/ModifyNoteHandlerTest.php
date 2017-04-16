<?php
declare(strict_types=1);

namespace EMA\Tests\Domain\Note\Commands\ModifyNote;

use EMA\Domain\Foundation\Exception\ModelNotFound;
use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Commands\ModifyNote\ModifyNote;
use EMA\Domain\Note\Commands\ModifyNote\ModifyNoteHandler;
use EMA\Domain\Note\Events\NoteModified;
use EMA\Domain\Note\Model\Collection\NoteCollection;
use EMA\Domain\Note\Model\Note;
use EMA\Domain\Note\Model\VO\NoteText;
use EMA\Tests\BaseTest;
use Faker\Factory;

class ModifyNoteHandlerTest extends BaseTest
{
    protected function setUp()
    {
        parent::setUp();
        container()->get(NoteCollection::class)->wipe();
        
        $this->restartContainer();
        $this->setAuthorizationAs(true);
    }
    
    function test_it_works()
    {
        $faker    = Factory::create();
        $id       = new Identity();
        $owner_id = new Identity();
        $text     = new NoteText($faker->text);
        $text2    = new NoteText($faker->text);
        
        $collection = container()->get(NoteCollection::class);
        
        $note = Note::make($id, $text, $owner_id);
        $collection->save($note);
        $this->assertEquals(1, $collection->all()->count());
        
        $handler = container()->get(ModifyNoteHandler::class);
        $handler->__invoke(new ModifyNote($text2, $id));
        
        $this->assertEquals(1, $collection->all()->count());
        $this->assertEquals($text2, $collection->all()->first()->getText());
    }
    
    function test_it_wont_let_update_missing_id()
    {
        $this->expectException(ModelNotFound::class);
        
        $faker = Factory::create();
        $id    = new Identity();
        $text  = new NoteText($faker->text);
        
        $handler = container()->get(ModifyNoteHandler::class);
        $handler->__invoke(new ModifyNote($text, $id));
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
        command_bus()->dispatch(new ModifyNote($text, $id));
        
        $this->assertTrue($logger->assertHasEventForAggregateId(NoteModified::class, $id));
    }
    
}
