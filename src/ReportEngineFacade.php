<?php

namespace AlwaysOpen\ReportEngine;

use Illuminate\Support\Facades\Facade;

/**
 * @see \AlwaysOpen\ReportEngine\ReportEngine
 */
class ReportEngineFacade extends Facade
{
    #[\Override]
    protected static function getFacadeAccessor()
    {
        return 'report-engine';
    }
}
