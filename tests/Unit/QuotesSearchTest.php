<?php

use Dustov\Quotes\QuotesManager;
use Dustov\Quotes\Services\BinarySearchService;

it('The manager delegates the search to the search service.', function(){
    $mockSearch= Mockery::mock(BinarySearchService::class);

    $mockSearch->shouldReceive('search')
    ->once()
    ->with(['id' => 1], 1)
    ->andReturn(['id' => 1, 'quote' => 'Fake Quote']);

    $manager = new QuotesManager($mockSearch);

    $result = $manager->searchById(['id' => 1], 1);

    expect($result['quote'])->toBe('Fake Quote');
});


