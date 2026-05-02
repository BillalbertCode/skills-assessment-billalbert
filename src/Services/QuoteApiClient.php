<?php

namespace Dustov\Quotes\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class QuoteApiClient
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('quotes.api_base_url');
    }

    public function fetchById(int $id): array
    {
        $response = Http::timeout(3)->get("{$this->baseUrl}/{$id}");

        //if id doesn't exist
        if ($response->failed()){
            throw new Exception('Error retrieving the quote from the external API.');
        }

        return $response->json();
    }

    public function fetchMany(int $limit = 30, int $skip = 0): array
    {
        $response = Http::timeout(3)->get($this->baseUrl, [
            'limit' => $limit,
            'skip' => $skip,
        ]);

        if ($response->failed()) {
            throw new \Exception('Error fetching batch from API.');
        }

        return $response->json();
    }
}
