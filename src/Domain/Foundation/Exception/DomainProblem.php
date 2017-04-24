<?php
declare(strict_types=1);

namespace EMA\Domain\Foundation\Exception;

class DomainProblem extends \DomainException
{
    use KnownProblem;
}
