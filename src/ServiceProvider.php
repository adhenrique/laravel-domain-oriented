<?php

namespace LaravelDomainOriented;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class ServiceProvider extends LaravelServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config.php' => config_path('laravel-domain-oriented.php'),
        ], 'config');

        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-domain-oriented'),
        ], 'lang');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config.php', 'laravel-domain-oriented'
        );
    }
}
