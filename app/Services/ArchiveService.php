<?php
namespace App\Services;

use App\Models\Client;
use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArchiveService
{
    protected $mongoClient;
    protected $collection;

    public function __construct(MongoDBService $mongoConnection )
    {
        $this->mongoClient = $mongoConnection->getClient();
        $databaseName="laravel_bd";
        $this->collection = $this->mongoClient->selectCollection($databaseName, 'archivage_dette');

    }
    

    public function archiveInMongoDB($dette)
    {
        // Insérer la dette dans la collection MongoDB
        $result = $this->collection->insertOne([
            'client_id' => $dette->client_id,
            'montant' => $dette->montant,
            'archived_at' => now()->toDateTimeString(),
            'paiements' => $dette->paiements->toArray(), // Inclure les paiements associés à la dette
            'articles' => $dette->articles->toArray(), // Si vous avez une relation avec les articles
        ]);

        return $result->isAcknowledged();
    }
    public function archiveDette($dette)
    {
        // Nommer la collection par date
        $collectionName = now()->format('Y-m-d');
    // Récupérer les informations du client
        $client = Client::find($dette->client_id);
        // Préparer les données à archiver
        $data = [
            'client' => [
                'id' => $client->id,
                'surnom' => $client->surnom,  // Assurer que ce champ existe dans le modèle Client
                'telephone' => $client->telephone,  // Assurer que ce champ existe dans le modèle Client
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

        // Insertion dans la collection MongoDB
        app('mongodb')->collection($collectionName)->insertOne([
            'client_id' => $dette->client_id,
            'dette_id' => $dette->id,
            'data' => $data,
            'archived_at' => now()->toDateTimeString(),
        ]);


        return true;
    }

public function getSoldesDettes()
{
    $query = "
        SELECT d.*
        FROM dettes d
        JOIN (
            SELECT dette_id, SUM(montant) as total_paiements
            FROM paiements
            GROUP BY dette_id
        ) p ON p.dette_id = d.id
        WHERE p.total_paiements = d.montant
    ";

    $dettes = DB::select(DB::raw($query));

    return $dettes;
}

}

