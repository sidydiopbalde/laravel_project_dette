<?php

namespace App\Repository;

use App\Models\Dette;

class DetteRepositoryImpl implements DetteRepository
{

    public function findArticlesByDetteId(int $id)
    {
        // Récupérer les articles associés à une dette via la relation avec 'articles'
        $dette = Dette::with('articles')->findOrFail($id);

        return $dette->articles;
    }
    public function findPaiementsByDetteId(int $id)
    {
        // Récupérer les paiements associés à la dette
        $dette = Dette::with('paiements')->findOrFail($id);
        return $dette->paiements;
    }
    public function getDettesByClientId(int $clientId)
    {
        // Récupérer les dettes du client sans détails supplémentaires
        return Dette::select('id', 'client_id', 'montant',)
                    ->where('client_id', $clientId)
                    ->get();
    }
    public function create(array $data)
    {
        return Dette::create($data);
    }

    public function update(int $id, array $data)
    {
        $debt = Dette::find($id);
        if ($debt) {
            $debt->update($data);
            return $debt;
        }
        return null;
    }

    public function delete(int $id)
    {
        $debt = Dette::find($id);
        if ($debt) {
            return $debt->delete();
        }
        return false;
    }

    public function findById(int $id)
    {
        // Récupérer la dette avec les relations client, articles et paiements
        return Dette::with('client')->findOrFail($id);
    }
    public function findByClient(int $clientId)
    {
        return Dette::where('client_id', $clientId)->get();
    }
}
