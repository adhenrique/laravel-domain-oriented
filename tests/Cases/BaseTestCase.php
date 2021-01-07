<?php

namespace LaravelDomainOriented\Tests\Cases;

use LaravelDomainOriented\ServiceProvider;
use Orchestra\Testbench\TestCase;

abstract class BaseTestCase extends TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app) {
        // perform environment setup
    }
}
