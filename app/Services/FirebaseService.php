<?php

namespace App\Services;

use App\Models\Client;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;

class FirebaseService implements ArchiveServiceCommunInterface
{
    protected $database;

    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(config('firebase.credentials_file'))
            ->withDatabaseUri('https://laravelgestionddette-default-rtdb.firebaseio.com/');  // Replace with your Firebase Realtime Database URL

        $this->database = $factory->createDatabase();
    }

    // public function getDatabase(): Database
    // {
    //     return $this->database;
    // }

    // public function store($request)
    // {
    //     $newData = $this->database->getReference(date('Y-m-d H:i:s'))->push($request);
    //     return response()->json($newData->getValue());
    // }
    public function archiveDette($dette)
    {
        // dd($dette);
        // Nommer la collection par date
        $collectionName = now()->format('Y-m-d');
        $client = Client::find($dette->client_id);
        // Préparer les données à archiver
        $data = [
            'client' => [
                'id' => $client->id,
                'dette' => [
                    'id' => $dette->id,
                    'montant' => $dette->montant,
                    'date' => $dette->created_at->format('Y-m-d'),
                    'paiements' => $dette->paiements->map(function ($paiement) {
                        return [
                            'id' => $paiement->id,
                            'montant' => $paiement->montant,
                            'date' => $paiement->created_at->format('Y-m-d'),
                        ];
                    })->toArray(),
                    'articles' => $dette->articles->map(function ($article) {
                        return [
                            'id' => $article->id,
                            'libelle' => $article->libelle,
                            'qte' => $article->pivot->qte_vente,
                            'prix_unitaire' => $article->pivot->prix_vente,
                        ];
                    })->toArray(),
                ],
            ],
        ];

        // Insertion dans la collection Firebase
        $this->database->getReference($collectionName)->push($data);

        return true;
    }
}