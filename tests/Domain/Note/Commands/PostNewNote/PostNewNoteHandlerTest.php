<?php
declare(strict_types=1);

namespace EMA\Tests\Domain\Note\Commands\PostNewNote;

use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Commands\PostNewNote\PostNewNote;
use EMA\Domain\Note\Commands\PostNewNote\PostNewNoteHandler;
use EMA\Domain\Note\Model\Collection\NoteCollection;
use EMA\Domain\Note\Model\VO\NoteText;
use EMA\Tests\BaseTest;
use Faker\Factory;

class PostNewNoteHandlerTest extends BaseTest
{
    
    function test_public_api()
    {
        
        $faker    = Factory::create();
        $id       = new Identity();
        $owner_id = new Identity();
        $text     = new NoteText($faker->text);
        
        // 1. command
        $command = new PostNewNote($text, $id, $owner_id);
    
        // 2. handle command directly
        $handler = container()->get(PostNewNoteHandler::class);
        $handler->__invoke($command);
        
        // 3. assert note created
        $collection = container()->get(NoteCollection::class);
        $this->assertEquals(1, $collection->all()->count());
        
    }
    
}
