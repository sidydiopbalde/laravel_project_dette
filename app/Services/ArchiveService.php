<?php
namespace App\Services;

use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArchiveService
{
    protected $mongoClient;
    protected $collection;

    public function __construct()
    {
        // Se connecter à MongoDB avec le client MongoDB
        // $this->mongoClient = new MongoClient(config('services.mongodb.uri'));
        // $this->collection = $this->mongoClient->selectCollection(config('services.mongodb.database'), 'archive_db');
        $this->mongoClient = new MongoClient(env('MONGODB_URI'));
        $databaseName = env('MONGO_DATABASE', 'laravel_bd');
        Log::info(env('MONGO_DATABASE'));
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

