<?php

namespace AlwaysOpen\ReportEngine;

use Closure;
use Illuminate\Routing\Route as RoutingRoute;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ReportEngineServiceProvider extends PackageServiceProvider
{
    #[\Override]
    public function configurePackage(Package $package): void
    {
        $package
            ->name('report-engine')
            ->hasViews();

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/report-engine.php' => config_path('report-engine.php'),
            ], 'config');
        }
    }

    #[\Override]
    public function bootingPackage(): void
    {
        RoutingRoute::macro('multiformat', function () {
            /**
             * @var \Illuminate\Routing\Route $this
             */
            return $this->setUri($this->uri() . '{dot?}{_format?}')
                ->where('dot', '\.')
                ->where('_format', '(' . implode('|', config('report-engine.allowed_multi_formats')) . ')?');
        });
    }
}
