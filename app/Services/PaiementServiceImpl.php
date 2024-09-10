<?php
namespace App\Services;

use App\Exceptions\ServiceException;
use App\Repository\PaiementRepository;
use Illuminate\Support\Facades\DB;

class PaiementServiceImpl implements PaiementService
{
    protected $paiementRepository;

    public function __construct(PaiementRepository $paiementRepository)
    {
        $this->paiementRepository = $paiementRepository;
    }

    public function effectuerPaiement(array $data)
    {
        DB::beginTransaction();

        try {
            // Récupérer la dette
            $dette = $this->paiementRepository->getDetteById($data['dette_id']);

            // Calculer la somme des paiements effectués sugetDetteByIdr cette dette
            $totalPaiements = $dette->paiements()->sum('montant');

            // Vérifier si la dette est déjà payée ou si le nouveau paiement dépasse le montant restant
            $montantRestant = $dette->montant - $totalPaiements;

            if ($montantRestant <= 0) {
                throw new ServiceException('Cette dette a déjà été totalement payée.');
            }

            if ($data['montant'] > $montantRestant) {
                throw new ServiceException('Le montant du paiement dépasse le montant restant de la dette.');
            }

            // Créer le paiement
            $paiement = $this->paiementRepository->createPaiement($data);

            // Mettre à jour la dette (montant payé)
            // $nouveauMontantRestant = $montantRestant - $data['montant'];
            // $this->paiementRepository->updateDette($dette->id, ['montant' => $nouveauMontantRestant]);

            DB::commit();

            return $paiement;
        } catch (ServiceException $e) {
            DB::rollBack();
            throw new ServiceException('Dette non trouvée avec l\'ID ' . $data['dette_id']);
        } 
    }
}

