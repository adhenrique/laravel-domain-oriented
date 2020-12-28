<?php

namespace LaravelDomainOriented\Tests;

use LaravelDomainOriented\ServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    public function setUp(): void {
        parent::setUp();
        // additional setup
    }

    protected function getPackageProviders($app) {
        return [
            ServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app) {
        // perform environment setup
    }
}
