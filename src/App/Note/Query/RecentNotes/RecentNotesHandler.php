<?php
declare(strict_types=1);

namespace EMA\App\Note\Query\RecentNotes;

use EMA\App\Note\Query\NoteFinder;
use React\Promise\Deferred;

class RecentNotesHandler
{
    
    /** @var  NoteFinder */
    private $finder;
    
    /**
     * RecentNotesHandler constructor.
     *
     * @param NoteFinder $finder
     */
    public function __construct(NoteFinder $finder) { $this->finder = $finder; }
    
    
    function __invoke(RecentNotes $query, Deferred $deferred = null)
    {
        $result = $this->finder->recent($query->getCount(), $query->getOwnerId());
        if (null === $deferred) {
            return $result;
        }
        
        $deferred->resolve($result);
    }
    
    
}
