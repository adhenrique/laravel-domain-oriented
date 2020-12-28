<?php

namespace LaravelDomainOriented;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('laravel-domain-oriented.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-domain-oriented'),
        ], 'lang');

        // only to test
        $this->loadRoutesFrom(__DIR__.'/../routes/test.php');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php', 'laravel-domain-oriented'
        );
    }
}
