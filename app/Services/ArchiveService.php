<?php
namespace App\Services;

use App\Models\Client;
use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArchiveService implements ArchiveServiceCommunInterface
{
    protected $mongoClient;
    protected $databaseName;

    public function __construct(MongoDBService $mongoConnection)
    {
        $this->mongoClient = $mongoConnection->getClient();
        $this->databaseName = 'laravel_bd';
    }
    
    public function archiveDette($dette)
    {
        try {
            // Récupérer les informations du client
            $client = Client::find($dette->client_id);
            if (!$client) {
                throw new \Exception('Client non trouvé');
            }

            // Préparer les données à archiver
            $data = [
                'client' => [
                    'id' => $client->id,
                    'surnom' => $client->surnom,  
                    'telephone' => $client->telephone,  
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

            // Nommer la collection par date
            $collectionName = now()->format('Y-m-d');

            // Insertion dans la collection MongoDB
            $collection = $this->mongoClient->selectCollection($this->databaseName, 'archivage_dette');
            $collection->insertOne([ 
                'client_id' => $dette->client_id,
                'dette_id' => $dette->id,
                'data' => $data,
                'archived_at' => now()->toDateTimeString(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'archivage de la dette : ' . $e->getMessage());
            return false;
        }
    }

    public function getSoldesDettes()
    {
        try {
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
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des dettes soldées : ' . $e->getMessage());
            return [];
        }
    }
}
