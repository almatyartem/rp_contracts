<?php

namespace ApiSdk\Facades;

use Illuminate\Support\Facades\Facade;

class FilesApi extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'filesapi';
    }
}
