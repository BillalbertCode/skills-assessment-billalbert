<?php

namespace Dustov\Quotes;

use Dustov\Quotes\Services\BinarySearchService;
use Dustov\Quotes\Services\QuoteApiClient;
use Dustov\Quotes\Services\RateLimiterService;
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
        $this->cacheTtl = config('quotes.cache.ttl', 86400);
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

        //Watch Cache
        $cacheData = Cache::get('quotes_collection', [
            'is_hydrated' => false,
            'data' => []
        ]);

        $quotes = $cacheData['data'];
        
        //search quotes in cache
        $quote = $this->searchService->search($quotes, $id);

        if($quote){
            return $quote;
        };

        //API call, need new quote 

        $newQuote = $this->apiClient->fetchById($id);

        //add new quote
        $quotes[] = $newQuote;

        //sort quotes 
        usort($quotes, fn($first,$second) => $first['id'] <=> $second['id']);

        Cache::put('quotes_collection', [
            'is_hydrated' => true,
            'data' => $quotes
        ], $this->cacheTtl);

        return $newQuote;
    }
}
