<?php

namespace BluefynInternational\ReportEngine;

use Closure;
use Illuminate\Routing\Route as RoutingRoute;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ReportEngineServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('report-engine')
            ->hasViews();
    }

    public function booting(Closure $callback)
    {
        RoutingRoute::macro('multiformat', function () {
            /**
             * @var \Illuminate\Routing\Route $this
             */
            return $this->setUri($this->uri() . '{dot?}{_format?}')->where('dot', '\.');
        });
    }
}
