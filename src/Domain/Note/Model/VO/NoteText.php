<?php
declare(strict_types=1);

namespace EMA\Domain\Note\Model\VO;

use Assert\Assert;

final class NoteText
{
    /** @var  string */
    private $text;
    
    /**
     * NoteText constructor.
     *
     * @param string $text
     */
    public function __construct($text)
    {
        $this->text = $text;
        Assert::that($text)->maxLength(10000);
    }
    
    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
    
    
}