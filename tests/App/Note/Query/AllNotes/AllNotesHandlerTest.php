<?php
declare(strict_types=1);

namespace EMA\Tests\App\Note\Query\AllNotes;

use Doctrine\Common\Collections\Collection;
use EMA\App\Note\Query\AllNotes\AllNotes;
use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Model\Collection\NoteCollection;
use EMA\Domain\Note\Model\Note;
use EMA\Domain\Note\Model\VO\NoteText;
use EMA\Tests\BaseTest;

class AllNotesHandlerTest extends BaseTest
{
    
    
    function test_it_gets_all_my_notes()
    {
        $this->restartContainer();
        
        $me = new Identity();
        $this->setAuthenticatedUser($me);
        
        container()->get(NoteCollection::class)->save(new Note(new Identity(), new NoteText(""), $me));
        container()->get(NoteCollection::class)->save(new Note(new Identity(), new NoteText(""), $me));
        container()->get(NoteCollection::class)->save(new Note(new Identity(), new NoteText(""), $me));
        
        $query = new AllNotes($me);
        query_bus()->dispatch($query)->then(function (Collection $result) {
            $this->assertEquals(3, $result->count());
        })->done();
        
    }
    
}
