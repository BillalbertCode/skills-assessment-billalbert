<?php

namespace Dustov\Quotes\Tests\Feature;

use Dustov\Quotes\Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;

class BatchImportCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget('quotes_collection');
        Cache::forget('rate_limit_global_quotes_limit');
    }

    #[Test]
    public function it_imports_quotes_using_the_artisan_command()
    {
        $baseUrl = config('quotes.api_base_url');

        Http::fake([
            $baseUrl . '*' => Http::response([
                'quotes' => [
                    ['id' => 1, 'quote' => 'Quote 1', 'author' => 'Author 1'],
                    ['id' => 2, 'quote' => 'Quote 2', 'author' => 'Author 2'],
                ],
                'total' => 2,
                'skip' => 0,
                'limit' => 2
            ], 200)
        ]);

        $this->artisan('quotes:batch-import 2')
            ->expectsOutput('import complete!')
            ->assertExitCode(0);

        $cache = Cache::get('quotes_collection');
        expect($cache['data'])->toHaveCount(2);
        expect($cache['is_hydrated'])->toBeTrue();
    }

    #[Test]
    public function it_handles_de_duplication_during_batch_import()
    {
        // Pre-fill cache with ID 1
        Cache::put('quotes_collection', [
            'is_hydrated' => true,
            'data' => [['id' => 1, 'quote' => 'Existing', 'author' => 'A']]
        ]);

        $baseUrl = config('quotes.api_base_url');

        Http::fake([
            $baseUrl . '*' => Http::response([
                'quotes' => [
                    ['id' => 1, 'quote' => 'Duplicate', 'author' => 'A'],
                    ['id' => 2, 'quote' => 'New', 'author' => 'B'],
                ],
                'total' => 2,
                'skip' => 0,
                'limit' => 2
            ], 200)
        ]);

        $this->artisan('quotes:batch-import 2')
            ->assertExitCode(0);

        $cache = Cache::get('quotes_collection');
        expect($cache['data'])->toHaveCount(2);
        expect($cache['data'][0]['id'])->toBe(1);
        expect($cache['data'][1]['id'])->toBe(2);
    }

    #[Test]
    public function it_stops_gracefully_when_reaching_api_total_limit()
    {
        $baseUrl = config('quotes.api_base_url');

        Http::fake([
            $baseUrl . '*' => Http::response([
                'quotes' => [
                    ['id' => 1, 'quote' => 'A', 'author' => 'A'],
                    ['id' => 2, 'quote' => 'B', 'author' => 'B'],
                    ['id' => 3, 'quote' => 'C', 'author' => 'C'],
                    ['id' => 4, 'quote' => 'D', 'author' => 'D'],
                    ['id' => 5, 'quote' => 'E', 'author' => 'E'],
                ],
                'total' => 5,
                'skip' => 0,
                'limit' => 5
            ], 200)
        ]);

        // Request 10, but only 5 exist
        $this->artisan('quotes:batch-import 10')
            ->assertExitCode(0);

        $cache = Cache::get('quotes_collection');
        expect($cache['data'])->toHaveCount(5);
    }
}
