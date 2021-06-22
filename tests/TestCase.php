<?php

namespace BluefynInternational\ReportEngine\Tests;

use BluefynInternational\ReportEngine\ReportEngineServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

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
