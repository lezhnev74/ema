<?php
declare(strict_types=1);

namespace EMA\Tests\Domain\Note\Commands\PostNewNote;

use Assert\InvalidArgumentException;
use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Commands\PostNewNote\PostNewNote;
use Faker\Factory;
use PHPUnit\Framework\TestCase;

class PostNewNoteTest extends TestCase
{
    
    function test_public_api()
    {
        
        $faker = Factory::create();
        
        $id       = new Identity();
        $owner_id = new Identity();
        $text     = $faker->text();
        
        $command = new PostNewNote($text, $id, $owner_id);
        $this->assertEquals($text, $command->getText());
        $this->assertTrue($id->isEqual($command->getId()));
        $this->assertTrue($owner_id->isEqual($command->getOwnerId()));
        
    }
    
    function test_it_validates_data()
    {
        $this->expectException(InvalidArgumentException::class);
        
        $id       = new Identity();
        $owner_id = new Identity();
        $text     = str_repeat('a', 10001); // exceeds max value
        
        new PostNewNote($text, $id, $owner_id);
        
    }
    
}
