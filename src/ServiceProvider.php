<?php

namespace LaravelDomainOriented;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Illuminate\Support\Str;
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

        $this->createDomainsFile();
        $this->registerPolicies();
    }

    public function register()
    {
        //
    }

    private function registerPolicies()
    {
        $domainNames = require (app_path('domains.php'));

        foreach ($domainNames as $domainName) {
            $namespace = 'App\\Domain\\'.$domainName;
            $policy = $namespace.'\\'.$domainName.'Policy';
            $tableName = Str::snake(Str::pluralStudly($domainName));

            Gate::policy($tableName, $policy);
        }
    }

    private function createDomainsFile()
    {
        $filePath = app_path('domains.php');
        $exists = File::exists($filePath);
        $stubFile = File::get(__DIR__.'/Stubs/domains.stub');

        if (!$exists) {
            File::put($filePath, $stubFile);
        }
    }
}
