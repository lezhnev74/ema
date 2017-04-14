<?php
declare(strict_types = 1);

namespace EMA\Tests\Domain\Note\Commands\DeleteNote;

use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Commands\DeleteNote\DeleteNote;
use EMA\Tests\BaseTest;

class DeleteNoteTest extends BaseTest {
    
    function test_public_api() {
        
        $id = new Identity();
        $command = new DeleteNote($id);
        
        $this->assertTrue($id->isEqual($command->getId()));
        
    }
    
}
