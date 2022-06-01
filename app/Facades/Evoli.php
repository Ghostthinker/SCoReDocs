<?php

namespace App\Facades;

use App\Services\EvoliService;
use Illuminate\Support\Facades\Facade;

class Evoli extends Facade
{
    protected static function getFacadeAccessor()
    {
        return EvoliService::class;
    }
}
