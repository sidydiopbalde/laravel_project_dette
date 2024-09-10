<?php
namespace App\Services;

interface PaiementService
{
    public function effectuerPaiement(array $data);
}
