<?php

namespace nosennij\LaravelCategory\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelCategory extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravelcategory';
    }
}
