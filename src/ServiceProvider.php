<?php

namespace LaravelDomainOriented;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use LaravelDomainOriented\Commands\CreateDomain;
use LaravelDomainOriented\Commands\RemoveDomain;

class ServiceProvider extends LaravelServiceProvider
{
    public function boot()
    {
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
        //
    }
}
