<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class DetteRepositoryFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'dette_repository';
    }
}