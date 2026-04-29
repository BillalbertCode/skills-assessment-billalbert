<?php

namespace Dustov\Quotes;

use Illuminate\Support\ServiceProvider;

class QuotesServiceProvider extends ServiceProvider{


    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/quotes.php','quotes'
        );
    }

    public function boot(): void
    {
        
    }
}