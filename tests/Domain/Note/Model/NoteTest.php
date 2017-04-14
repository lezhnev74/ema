<?php
declare(strict_types=1);

namespace EMA\Tests\Domain\Note\Model\VO;

use Assert\InvalidArgumentException;
use Carbon\Carbon;
use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Model\Note;
use EMA\Domain\Note\Model\VO\NoteText;
use EMA\Tests\BaseTest;
use Faker\Factory;

final class NoteTest extends BaseTest
{
    function test_public_api()
    {
        Carbon::setTestNow();
        
        $faker    = Factory::create();
        $id       = new Identity();
        $owner_id = new Identity();
        $text     = new NoteText($faker->text());
        
        $note = new Note($id, $text, $owner_id);
        $this->assertTrue($id->isEqual($note->getId()));
        $this->assertTrue($owner_id->isEqual($note->getOwnerId()));
        $this->assertEquals($text, $note->getText());
        $this->assertEquals(Carbon::now(), $note->getPostedAt());
        $this->assertNull($note->getModifiedAt());
    }
    
}