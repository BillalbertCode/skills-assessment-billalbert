<?php

namespace Dustov\Quotes;

use Dustov\Quotes\Commands\BatchImportCommand;
use Illuminate\Support\ServiceProvider;

class QuotesServiceProvider extends ServiceProvider
{


    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/quotes.php',
            'quotes'
        );
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                BatchImportCommand::class,
            ]);

            $this->publishes([
                __DIR__ . '/../config/quotes.php' => config_path('quotes.php'),
            ], 'quotes-config');
        }

        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');


        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'quotes');
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        $this->publishes([
            __DIR__ . '/../public' => public_path('vendor/quotes'),
        ], 'quotes-assets');
    }
}
