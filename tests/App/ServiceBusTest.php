<?php
declare(strict_types=1);

namespace EMA\Tests\App;

use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Commands\PostNewNote\PostNewNote;
use EMA\Domain\Note\Model\Collection\NoteCollection;
use EMA\Domain\Note\Model\VO\NoteText;
use EMA\Tests\BaseTest;

final class ServiceBusTest extends BaseTest
{
    function test_direct_routing()
    {
        // 1. command
        $command = new PostNewNote(new NoteText(""), new Identity(), new Identity());
        
        // 2. handle command directly
        command_bus()->dispatch($command);
        
        // 3. assert note created
        $collection = container()->get(NoteCollection::class);
        $this->assertEquals(1, $collection->all()->count());
        
    }
}