<?php

use Dustov\Quotes\Services\QuoteApiClient;
use Illuminate\Support\Facades\Http;


it('successfully obtains a quote from the fake API', function () {

    $pattern = config('quotes.api_base_url') . '/*';


    Http::fake([
        $pattern => Http::response([
            'id' => 1,
            'quote' => 'Fake Quote',
            'author' => 'Fake Author'
        ], 200)
    ]);

    $client = new QuoteApiClient();
    $result = $client->fetchById(1);

    expect($result['id'])->toBe(1);
    expect($result['quote'])->toBe('Fake Quote');
});
