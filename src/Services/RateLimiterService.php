<?php

namespace Dustov\Quotes\Services;

use Dustov\Quotes\Exceptions\RateLimitExceeded;
use Illuminate\Support\Facades\Cache;

class RateLimiterService
{
    protected string $cachePrefix = 'rate_limit_';
    protected int $maxAttempts;
    protected int $windowInSeconds;

    public function __construct()
    {
        $this->maxAttempts = config('quotes.rate_limit.max_attempts');
        $this->windowInSeconds = config('quotes.rate_limit.window_in_seconds');
    }

    public function checkLimit(string $key)
    {
        $fullKey = $this->cachePrefix . $key;

        $current = Cache::get($fullKey, 0);
        
        //Exception fast fail
        if ($current >= $this->maxAttempts) {
            throw new RateLimitExceeded();
        }

        if($current === 0 ){
            Cache::put($fullKey, 1, $this->windowInSeconds);
        } else{
            Cache::increment($fullKey);
        }
    }
}
