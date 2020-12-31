<?php

namespace LaravelDomainOriented;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use LaravelDomainOriented\Commands\CreateDomain;
use LaravelDomainOriented\Commands\RemoveDomain;

class ServiceProvider extends LaravelServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/config.php' => config_path('laravel-domain-oriented.php'),
        ], 'config');

        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'lang');
        $this->publishes([
            __DIR__.'/../resources/lang' => resource_path('lang/vendor/laravel-domain-oriented'),
        ], 'lang');

        $this->commands([
            CreateDomain::class,
            RemoveDomain::class,
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/config.php', 'laravel-domain-oriented'
        );
    }
}
