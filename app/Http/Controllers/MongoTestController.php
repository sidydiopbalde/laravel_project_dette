<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dette;

class MongoTestController extends Controller
{
    public function archiveSoldedDettes()
    {
        // Récupérer les dettes soldées (par exemple, dettes avec un statut "soldée" ou quand le montant restant est zéro)
        $soldedDettes = Dette::where('statut', 'soldée')->get();

        // Connexion MongoDB
        $mongo = app('mongodb');
        $collection = $mongo->dettes_archive;

        // Archiver chaque dette soldée dans MongoDB
        foreach ($soldedDettes as $dette) {
            $collection->insertOne([
                'client_id' => $dette->client_id,
                'montant' => $dette->montant,
                'date_soldée' => now(),
                'articles' => $dette->articles->toArray(),  // Si vous avez des relations avec des articles
                'paiements' => $dette->paiements->toArray() // Si vous avez des relations avec des paiements
            ]);

            // Supprimer la dette de la base SQL ou la marquer comme archivée
            $dette->delete();
        }

        return response()->json(['message' => 'Dettes soldées archivées avec succès dans MongoDB']);
    }
}
