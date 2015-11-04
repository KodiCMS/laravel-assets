<?php

namespace KodiCMS\Assets\Facades;

use Illuminate\Support\Facades\Facade;

class Meta extends Facade
{
    /**
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return 'assets.meta';
    }
}
