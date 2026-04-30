<?php

namespace Dustov\Quotes;

use Dustov\Quotes\Services\BinarySearchService;

class QuotesManager
{
    protected BinarySearchService $searchService;

    public function __construct(BinarySearchService $searchService)
    {
        $this->searchService = $searchService;
    }

    public function searchById(array $quotes, int $targetId): ?array
    {
        return $this->searchService->search($quotes, $targetId);
    }
}
