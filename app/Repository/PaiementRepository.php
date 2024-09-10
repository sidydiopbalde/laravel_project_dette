<?php

namespace App\Repository;

interface PaiementRepository
{
    public function createPaiement(array $data);
    public function getDetteById(int $id);
    public function updateDette(int $id, array $data);
}
