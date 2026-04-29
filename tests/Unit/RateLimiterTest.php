<?php

use Dustov\Quotes\Exceptions\RateLimitExceeded;
use Dustov\Quotes\Services\RateLimiterService;

it('allows requests within the limit', function () {

    //We configured the rate limit
    config(['quotes.rate_limit.max_attempts' => 2]);

    $service = new RateLimiterService();

    $service->checkLimit('usuario_1');
    $service->checkLimit('usuario_1');

    expect(true)->toBeTrue();
});

it('It shows an exception when the limit is exceeded.', function () {

    config(['quotes.rate_limit.max_attempts' => 2]);

    $service = new RateLimiterService();

    $service->checkLimit('usuario_2');
    $service->checkLimit('usuario_2');
    $service->checkLimit('usuario_2');
    
})->throws(RateLimitExceeded::class);
