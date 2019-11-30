<?php

namespace ApiSdk\Facades;

use Illuminate\Support\Facades\Facade;

class ReportsApi extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'reportsapi';
    }
}
