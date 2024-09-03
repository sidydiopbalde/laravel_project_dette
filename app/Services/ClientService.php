<?php

namespace App\Services;

interface ClientService
{
    public function getClientByPhone(string $phone);
    public function createClient(array $data);
    public function getAllClients(array $filters);
    public function getClientById(int $id, bool $includeUser);
    public function findByTelephone(string $telephone);
    // Ajoutez d'autres méthodes si nécessaire
}

