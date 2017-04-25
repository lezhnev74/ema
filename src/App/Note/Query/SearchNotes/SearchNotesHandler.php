<?php
declare(strict_types=1);

namespace EMA\App\Note\Query\SearchNotes;

use EMA\App\Note\Query\NoteFinder;
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
