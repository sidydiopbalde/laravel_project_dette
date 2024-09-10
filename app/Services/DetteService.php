<?php

namespace App\Services;

use Illuminate\Http\Request;

interface DetteService
{
    public function createDette(array $data);
    public function addArticlesToDette(array $articlesData, int $detteId); 
    public function getArticlesByDetteId(int $id);
    public function getPaiementsByDetteId(int $id);
    public function getClientDettes(int $clientId);
    // public function updateDebt(int $id, array $data);
    // public function deleteDebt(int $id);
     public function getDetteById(int $id);
    // public function getDebtsByClient(int $clientId);
}
