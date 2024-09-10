<?php

namespace App\Repository;

interface DetteRepository
{
    public function create(array $data);
    public function findArticlesByDetteId(int $id);
    public function findPaiementsByDetteId(int $id);
    public function getDettesByClientId(int $clientId);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function findById(int $id);
    public function findByClient(int $clientId);
    
}
