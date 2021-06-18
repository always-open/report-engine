<?php

namespace BluefynInternational\ReportEngine\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use BluefynInternational\ReportEngine\ReportEngineServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            ReportEngineServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'mysql');
        config()->set('database.connections.mysql.database', 'test');
        config()->set('database.connections.mysql.host', 'report-engine-db');
        config()->set('database.connections.mysql.database', 'test');
        config()->set('database.connections.mysql.username', 'root');
        config()->set('database.connections.mysql.password', 'test');
    }
}
