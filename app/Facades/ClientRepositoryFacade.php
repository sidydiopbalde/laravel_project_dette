<?php
namespace App\Facades;

use Illuminate\Support\Facades\Facade;
use App\Repository\ClientRepository;

class ClientRepositoryFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'Client_repository';
    }
}
