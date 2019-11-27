<?php

namespace ApiSdk\Facades;

use Illuminate\Support\Facades\Facade;

class CoreApi extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'coreapi';
    }
}
