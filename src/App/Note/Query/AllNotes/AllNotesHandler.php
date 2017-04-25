<?php
declare(strict_types=1);

namespace EMA\App\Note\Query\AllNotes;

use EMA\App\Note\Query\NoteFinder;
use React\Promise\Deferred;

class AllNotesHandler
{
    /** @var  AllNotes */
    private $query;
    /** @var  NoteFinder */
    private $finder;
    
    /**
     * AllNotesHandler constructor.
     *
     * @param AllNotes   $query
     * @param NoteFinder $finder
     */
    public function __construct(AllNotes $query, NoteFinder $finder)
    {
        $this->query  = $query;
        $this->finder = $finder;
    }
    
    
    public function __invoke(AllNotes $query, Deferred $deferred = null)
    {
        $result = $this->finder->all();
        if (null === $deferred) {
            return $result;
        }
        
        $deferred->resolve($result);
    }
}
