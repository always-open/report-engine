<?php

namespace BluefynInternational\ReportEngine;

use Illuminate\Support\Facades\Facade;

/**
 * @see \BluefynInternational\ReportEngine\ReportEngine
 */
class ReportEngineFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'report-engine';
    }
}
