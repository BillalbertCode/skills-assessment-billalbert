<?php

use Dustov\Quotes\Services\BinarySearchService;

$quotes = [
    ['id' => 10, 'quote' => 'Ten', 'author' => 'Romulo Gallegos'],
    ['id' => 20, 'quote' => 'Twenty', 'author' => 'Miranda'],
    ['id' => 30, 'quote' => 'Thirty', 'author' => 'Tyron Gonzales Orama'],
    ['id' => 40, 'quote' => 'Forty', 'athor' => 'Humberto Fernández Morán'],
];

it('finds a quote in the middle of the array', function () use ($quotes) {
    $service = new BinarySearchService();
    $result = $service->search($quotes, 20);

    expect($result)->toBe($quotes[1]);
});

it('finds a quote at the beginning of the array', function () use ($quotes) {
    $service = new BinarySearchService();
    $result = $service->search($quotes, 10);

    expect($result)->toBe($quotes[0]);
});

it('finds a quote at the end of the array', function () use ($quotes) {
    $service = new BinarySearchService();
    $result = $service->search($quotes, 40);

    expect($result)->toBe($quotes[3]);
});

it('returns null if the ID is less than the minimum', function () use ($quotes) {
    $service = new BinarySearchService();
    $result = $service->search($quotes, 5);

    expect($result)->toBeNull();
});

it('returns null if the ID is greater than the maximum', function () use ($quotes) {
    $service = new BinarySearchService();
    $result = $service->search($quotes, 50);

    expect($result)->toBeNull();
});

it('handles an empty array correctly', function () {
    $service = new BinarySearchService();
    $result = $service->search([], 10);

    expect($result)->toBeNull();
});
