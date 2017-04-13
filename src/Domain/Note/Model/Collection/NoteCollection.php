<?php
declare(strict_types=1);


namespace EMA\Domain\Note\Model\Collection;


use Doctrine\Common\Collections\Collection;
use EMA\Domain\Foundation\VO\Identity;
use EMA\Domain\Note\Model\Note;

interface NoteCollection
{
    public function all($page = 1, $on_page = 100): Collection;
    
    public function findById(Identity $id): Note;
    
    public function save(Note $note): void;
    
    public function delete(Identity $id): void;
}