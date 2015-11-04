<?php

namespace KodiCMS\Assets\Facades;

use Illuminate\Support\Facades\Facade;

class Assets extends Facade
{
    /**
     * @return string
     */
    public static function getFacadeAccessor()
    {
        return 'assets';
    }
}
