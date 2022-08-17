<?php

namespace AlwaysOpen\ReportEngine;

use Illuminate\Support\Facades\Facade;

/**
 * @see \AlwaysOpen\ReportEngine\ReportEngine
 */
class ReportEngineFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'report-engine';
    }
}
