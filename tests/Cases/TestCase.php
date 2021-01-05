<?php

namespace LaravelDomainOriented\Tests\Cases;

use LaravelDomainOriented\ServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void {
        parent::setUp();
        // additional setup
    }

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
