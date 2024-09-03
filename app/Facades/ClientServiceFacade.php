<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Services\ClientService;

class ClientServiceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Client_service';
    }
}
