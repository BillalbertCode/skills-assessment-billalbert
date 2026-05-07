<?php

namespace Dustov\Quotes\Tests\Feature;

use Dustov\Quotes\QuotesManager;
use Dustov\Quotes\Exceptions\RateLimitExceeded;
use Dustov\Quotes\Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Exception;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;

class QuotesManagerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget('quotes_collection');
        Cache::forget('rate_limit_global_quotes_limit');
    }

    #[Test]
    public function it_retrieves_a_quote_from_api_and_caches_it_ordered()
    {
        $baseUrl = config('quotes.api_base_url');
        
        Http::fake([
            "$baseUrl/50" => Http::response([
                'id' => 50,
                'quote' => 'Test Quote',
                'author' => 'Test Author'
            ], 200)
        ]);

        $manager = app(QuotesManager::class);
        $result = $manager->getQuote(50);

        expect($result['id'])->toBe(50);
        expect($result['quote'])->toBe('Test Quote');
        
        $cache = Cache::get('quotes_collection');
        expect($cache['is_hydrated'])->toBeTrue();
        expect($cache['data'])->toHaveCount(1);
        expect($cache['data'][0]['id'])->toBe(50);
    }

    #[Test]
    public function it_throws_exception_when_api_returns_404()
    {
        $baseUrl = config('quotes.api_base_url');
        
        Http::fake([
            "$baseUrl/999" => Http::response([], 404)
        ]);

        $manager = app(QuotesManager::class);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Error retrieving the quote from the external API.');

        $manager->getQuote(999);
    }

    #[Test]
    public function it_respects_rate_limiting()
    {
        config(['quotes.rate_limit.max_attempts' => 1]);
        $baseUrl = config('quotes.api_base_url');
        
        Http::fake([
            "$baseUrl/*" => Http::response(['id' => 1, 'quote' => 'Q', 'author' => 'A'], 200)
        ]);

        $manager = app(QuotesManager::class);
        
        // First request passes
        $manager->getQuote(1);

        // Second request should fail
        $this->expectException(RateLimitExceeded::class);
        $manager->getQuote(2);
    }

    #[Test]
    public function it_returns_cached_quotes_even_when_rate_limit_is_exceeded()
    {
        config(['quotes.rate_limit.max_attempts' => 0]);
        Cache::put('quotes_collection', [
            'is_hydrated' => true,
            'data' => [
                ['id' => 1, 'quote' => 'Cached Quote', 'author' => 'Cached Author'],
            ],
        ]);

        $manager = app(QuotesManager::class);

        $result = $manager->getQuote(1);

        expect($result['id'])->toBe(1);
        expect($result['quote'])->toBe('Cached Quote');
    }

    #[Test]
    public function it_validates_that_id_is_positive()
    {
        $manager = app(QuotesManager::class);

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The ID must be a positive integer.');

        $manager->getQuote(0);
    }
}
