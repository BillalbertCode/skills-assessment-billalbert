<?php

namespace Dustov\Quotes\Tests;

use Dustov\Quotes\QuotesServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{

    protected function getPackageProviders($app)
    {
        return [
            QuotesServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('cache.default', 'array');
        $app['config']->set('quotes.cache_store', 'array');
    }
}
