<?php
declare(strict_types=1);

namespace EMA\App\Query\Note\SearchNotes;

use EMA\App\Query\Note\NoteFinder;
use React\Promise\Deferred;

class SearchNotesHandler
{
    /** @var  NoteFinder */
    private $finder;
    
    /**
     * SearchNotesHandler constructor.
     *
     * @param NoteFinder  $finder
     */
    public function __construct(NoteFinder $finder)
    {
        $this->finder = $finder;
    }
    
    
    public function __invoke(SearchNotes $query, Deferred $deferred = null)
    {
        $result = $this->finder->search($query->getQuery(), $query->getOwnerId());
        if (null === $deferred) {
            return $result;
        }
        
        $deferred->resolve($result);
    }
}
