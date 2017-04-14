<?php
declare(strict_types=1);

namespace EMA\Tests\Domain\Note\Model\VO;

use Assert\InvalidArgumentException;
use EMA\Domain\Note\Model\VO\NoteText;
use EMA\Tests\BaseTest;

final class NoteTextTest extends BaseTest
{
    function test_public_api()
    {
        $note_text = new NoteText("text");
        $this->assertEquals("text", $note_text->getText());
    }
    
    function test_it_validates_data()
    {
        $this->expectException(InvalidArgumentException::class);
        $note_text = new NoteText(str_repeat("a", 10001));
    }
}