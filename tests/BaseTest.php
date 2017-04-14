<?php
declare(strict_types=1);

namespace EMA\Tests;

use EMA\Domain\Note\Model\Collection\NoteCollection;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    protected function setUp()
    {
        parent::setUp();
        
        container()->get(NoteCollection::class)->wipe();
        
    }
    
}