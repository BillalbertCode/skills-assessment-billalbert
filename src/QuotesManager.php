<?php

namespace Dustov\Quotes;

use Dustov\Quotes\Services\BinarySearchService;
use Dustov\Quotes\Services\QuoteApiClient;
use Dustov\Quotes\Services\RateLimiterService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class QuotesManager
{
    protected BinarySearchService $searchService;
    protected RateLimiterService $rateLimiter;
    protected QuoteApiClient $apiClient;
    protected int $cacheTtl;

    public function __construct(
        RateLimiterService $rateLimiter,
        QuoteApiClient $apiClient,
        BinarySearchService $searchService
    ) {
        $this->searchService = $searchService;
        $this->rateLimiter = $rateLimiter;
        $this->apiClient = $apiClient;
        $this->cacheTtl = config('quotes.cache_ttl', 86400);
    }

    public function searchById(array $quotes, int $targetId): ?array
    {
        return $this->searchService->search($quotes, $targetId);
    }

    public function getQuote(int $id)
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException("The ID must be a positive integer.");
        }

        $this->rateLimiter->checkLimit('global_quotes_limit');

        $quotes = $this->getCachedQuotes();

        //search quotes in cache
        $quote = $this->searchService->search($quotes, $id);

        if ($quote) {
            return $quote;
        };

        //API call, need new quote 
        $newQuote = $this->apiClient->fetchById($id);

        //add new quote
        $quotes[] = $newQuote;

        //sort quotes 
        usort($quotes, fn($first, $second) => $first['id'] <=> $second['id']);

        Cache::put('quotes_collection', [
            'is_hydrated' => true,
            'data' => $quotes
        ], $this->cacheTtl);

        return $newQuote;
    }

    public function addBatch(array $newQuote): int
    {
        $existingQuotes = $this->getCachedQuotes();

        //only extract the IDs 
        $existingIds = array_column($existingQuotes, 'id');

        $addedCount = 0;

        foreach ($newQuote as $quote) {
            //Only add it if the ID doesn't exist.
            if (!in_array($quote['id'], $existingIds)) {
                $existingQuotes[] = $quote;
                $addedCount++;
            }
        }

        if ($addedCount > 0) {
            // Order by binary search
            usort($existingQuotes, fn($a, $b) => $a['id'] <=> $b['id']);

            Cache::put('quotes_collection', [
                'is_hydrated' => true,
                'data' => $existingQuotes
            ], $this->cacheTtl);
        }

        return $addedCount;
    }

    public function getCachedQuotes(): array
    {
        $cacheData = Cache::get('quotes_collection', [
            'is_hydrated' => false,
            'data' => []
        ]);

        return $cacheData['data'];
    }

    public function paginateQuotes(?int $page = null, ?int $perPage = null): LengthAwarePaginator
    {
        $quotes = $this->getCachedQuotes();

        $page = max(1, $page ?? 1);
        $perPage = max(1, $perPage ?? config('quotes.per_page_default', 10));

        return new LengthAwarePaginator(
            array_slice($quotes, ($page - 1) * $perPage, $perPage),
            count($quotes),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
    }
}
