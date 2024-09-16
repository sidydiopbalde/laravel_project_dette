<?php

namespace App\Services;

use App\Http\Requests\StoreRequest;
use App\Models\Client;

interface ClientService
{
    public function getClientByPhone(string $phone);
    public function createClient(StoreRequest $request);
    public function getAllClients(array $filters);
    public function getClientById(int $id);
    public function findByTelephone(string $telephone);
    public function notifyClientsWithDebts();
    // Ajoutez d'autres méthodes si nécessaire
}

