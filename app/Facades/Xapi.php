<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Xapi extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Xapi';
    }
}
