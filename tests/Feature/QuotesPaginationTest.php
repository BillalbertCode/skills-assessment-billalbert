<?php

namespace Dustov\Quotes\Tests\Feature;

use Dustov\Quotes\QuotesManager;
use Dustov\Quotes\Tests\TestCase;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;

class QuotesPaginationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget('quotes_collection');
    }

    #[Test]
    public function it_paginates_cached_quotes_correctly()
    {
        // 1. Pre-fill cache with 5 quotes
        $quotes = [
            ['id' => 1, 'quote' => 'Q1', 'author' => 'A1'],
            ['id' => 2, 'quote' => 'Q2', 'author' => 'A2'],
            ['id' => 3, 'quote' => 'Q3', 'author' => 'A3'],
            ['id' => 4, 'quote' => 'Q4', 'author' => 'A4'],
            ['id' => 5, 'quote' => 'Q5', 'author' => 'A5'],
        ];

        Cache::put('quotes_collection', [
            'is_hydrated' => true,
            'data' => $quotes
        ]);

        $manager = app(QuotesManager::class);

        // 2. Request page 1 with 2 items
        $result = $manager->paginateQuotes(1, 2);

        expect($result->items())->toHaveCount(2);
        expect($result->total())->toBe(5);
        expect($result->currentPage())->toBe(1);
        expect($result->lastPage())->toBe(3);
        expect($result->items()[0]['id'])->toBe(1);
        expect($result->items()[1]['id'])->toBe(2);
    }

    #[Test]
    public function it_automatically_corrects_invalid_page_numbers()
    {
        $quotes = [['id' => 1], ['id' => 2]];
        Cache::put('quotes_collection', ['is_hydrated' => true, 'data' => $quotes]);

        $manager = app(QuotesManager::class);

        // Request page -5
        $result = $manager->paginateQuotes(-5, 10);

        expect($result->currentPage())->toBe(1);
    }

    #[Test]
    public function it_uses_the_default_per_page_configuration()
    {
        // Set a custom default in config for the test
        config(['quotes.per_page_default' => 3]);

        $quotes = [['id' => 1], ['id' => 2], ['id' => 3], ['id' => 4]];
        Cache::put('quotes_collection', ['is_hydrated' => true, 'data' => $quotes]);

        $manager = app(QuotesManager::class);

        // Request page 1 without specifying per_page
        $result = $manager->paginateQuotes(1, null);

        expect($result->perPage())->toBe(3);
        expect($result->items())->toHaveCount(3);
    }

    #[Test]
    public function it_returns_cached_quotes_via_api_endpoint()
    {
        $quotes = [
            ['id' => 1, 'quote' => 'Cache Quote One', 'author' => 'Author One'],
            ['id' => 2, 'quote' => 'Cache Quote Two', 'author' => 'Author Two'],
        ];

        Cache::put('quotes_collection', [
            'is_hydrated' => true,
            'data' => $quotes,
        ]);

        $response = $this->getJson('/api/quotes?page=1');

        $response->assertOk();
        $response->assertJsonCount(2, 'data');
        $response->assertJsonPath('data.0.id', 1);
        $response->assertJsonPath('data.1.id', 2);
    }
}
