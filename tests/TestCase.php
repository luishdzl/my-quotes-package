<?php

namespace Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Vendor\MyQuotesPackage\Providers\QuoteServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            QuoteServiceProvider::class,
        ];
    }
}