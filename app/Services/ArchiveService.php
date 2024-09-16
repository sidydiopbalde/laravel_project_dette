<?php
namespace App\Services;
use App\Models\Article;
use App\Models\Client;
use App\Models\Dette;
use App\Models\Paiement;
use MongoDB\Client as MongoClient;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\UTCDateTime;
use MongoDB\BSON\ObjectId;
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
    public function getArchivedDettes($filter = [])
    {
        try {
            $collection = $this->mongoClient->selectCollection($this->databaseName, 'archivage_dette');
            $query = [];
    
            // Filtrer par client_id si fourni
            if (!empty($filter['client_id'])) {
                $query['client_id'] = (int)$filter['client_id']; // Convertir en entier pour correspondre au type attendu
            }
    
            // Filtrer par date si fourni
            if (!empty($filter['date'])) {
                $startOfDay = $filter['date'] . 'T00:00:00Z'; // Début de la journée en format ISO-8601
                $endOfDay = $filter['date'] . 'T23:59:59Z';   // Fin de la journée en format ISO-8601
            
                $query['archived_at'] = [
                    '$gte' => $startOfDay,
                    '$lte' => $endOfDay
                ];
            }
    
            // Exécuter la requête
            $dettes = $collection->find($query)->toArray();
    
            return [
                'success' => true,
                'message' => 'Dettes archivées récupérées avec succès',
                'data' => $dettes
            ];
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des dettes archivées depuis MongoDB : ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Erreur lors de la récupération des dettes archivées',
                'details' => $e->getMessage()
            ];
        }
    }
    
    
    public function getArchivedDettesByClient($clientId)
    {
        try {
            // Sélectionner la collection 'archivage_dette'
            $collection = $this->mongoClient->selectCollection($this->databaseName, 'archivage_dette');
    
            // Construire la requête pour filtrer par client_id
            $query = ['client_id' => (int) $clientId];  // Assure-toi que le type de client_id est correct
    
            // Récupérer les dettes filtrées par client_id
            $dettes = $collection->find($query)->toArray();
    
            // Vérifier si des dettes existent
            if (empty($dettes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune dette trouvée pour ce client.',
                    'data' => [],
                ], 404);
            }
    
            // Retourner les dettes trouvées
            return response()->json([
                'success' => true,
                'message' => 'Dettes archivées récupérées avec succès',
                'data' => $dettes,
            ], 200);
        } catch (\Exception $e) {
            // En cas d'erreur, log et renvoie un message d'erreur
            Log::error('Erreur lors de la récupération des dettes archivées depuis MongoDB : ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des dettes archivées',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    

    public function getArchivedDetteDetailsById($detteId)
    {
        try {
            // Sélectionner la collection 'archivage_dette'
            $collection = $this->mongoClient->selectCollection($this->databaseName, 'archivage_dette');

            // Convertir l'ID de la dette en ObjectId
            $dette = $collection->findOne(['dette_id' => (int)$detteId]);
            // Vérifier si la dette existe
            if (!$dette) {
                return response()->json(['message' => 'Dette non trouvée'], 404);
            }
            
            // Extraire les données de la dette depuis le champ 'data'
            $data = $dette['data'];
            $detteDetails = [
                'id' => $data['client']['dette']['id'],
                'montant' => $data['client']['dette']['montant'],
                'date' => $data['client']['dette']['date'],
                'client' => [
                    'id' => $data['client']['id'],
                    'surnom' => $data['client']['surnom'],
                    // 'telephone' => $data['client']['telephone'],
                ],
                'paiements' => $data['client']['dette']['paiements'],
                'articles' => $data['client']['dette']['articles'],
            ];
            // dd($detteDetails);
            
            // Retourner les informations de la dette
            return response()->json([
                'success' => true,
                'message' => 'Détails de la dette récupérés avec succès',
                'data' => $detteDetails,
            ], 200);
        } catch (\Exception $e) {
            // Enregistrer l'erreur et retourner une réponse d'erreur
            Log::error('Erreur lors de la récupération de la dette archivée depuis MongoDB : ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des détails de la dette',
                'details' => $e->getMessage(),
            ], 500);
        }
    }   
    public function restoreArchivedDetteById($detteId)
        {
            try {
                // Sélectionner la collection d'archivage
                $collection = $this->mongoClient->selectCollection($this->databaseName, 'archivage_dette');
        
                // Trouver la dette archivée
                $dette = $collection->findOne(['dette_id' => (int)$detteId]);
                // dd($dette);
                
                if (!$dette) {
                    throw new \Exception('Dette non trouvée dans les archives');
                }
        
                // Préparer les données pour l'insertion dans PostgreSQL
                $data = $dette['data'];
                // dd($data);
                
                // Vérifier et restaurer les informations du client
                $client = Client::find($data['client']['id']);
                if (!$client) {
                    throw new \Exception('Client non trouvé dans PostgreSQL');
                }
        
                // Restaurer la dette dans la table des dettes PostgreSQL
                $restoredDette = Dette::create([
                    'client_id' => $data['client']['id'],
                    'montant' => $data['client']['dette']['montant'],
                    'created_at' => $data['client']['dette']['date'],
                    // Ajoute d'autres champs si nécessaire
                ]);
                // Restaurer les paiements associés
                // foreach ($data['dette']['paiements'] as $paiement) {
                //     Paiement::create([
                //         'dette_id' => $restoredDette->id,
                //         'montant' => $paiement['montant'],
                //         'created_at' => $paiement['date'],
                //         // Ajoute d'autres champs si nécessaire
                //     ]);
                // }
        
                // Restaurer les articles associés
                // foreach ($data['dette']['articles'] as $article) {
                //     $articleModel = Article::find($article['id']);
                //     if ($articleModel) {
                //         $restoredDette->articles()->attach($articleModel->id, [
                //             'qte_vente' => $article['qte'],
                //             'prix_vente' => $article['prix_unitaire'],
                //         ]);
                //     }
                // }
        
                // Supprimer la dette des archives MongoDB
                //$collection->deleteOne(['dette_id' => $detteId]);
        
                return true;
            } catch (\Exception $e) {
                Log::error('Erreur lors de la restauration de la dette : ' . $e->getMessage());
                return false;
            }
    }
    

    public function restoreArchivedDettesByDate($date)
    {
        try {
            // Sélectionner la collection 'archivage_dette' dans MongoDB
            $collection = $this->mongoClient->selectCollection($this->databaseName, 'archivage_dette');
    
            // Rechercher les dettes archivées à la date spécifiée
            $dettesCursor = $collection->find([
                'archived_at' => ['$regex' => "^$date"] // Utiliser la date pour filtrer les documents
            ]);
            // Vérifier si des dettes ont été trouvées
            $restoredCount = 0;
            foreach ($dettesCursor as $dette) {
                // Extraire les données de la dette depuis le champ 'data'
                $data = $dette['data'];
                
                // Récupérer les informations du client depuis PostgreSQL
                $client = Client::find($data['client']['id']);
                // dd($client);
                if (!$client) {
                    throw new \Exception('Client non trouvé dans PostgreSQL pour la dette ID: ' . $dette['dette_id']);
                }
    
                // Restaurer la dette dans PostgreSQL
                $restoredDette = Dette::create([
                    'client_id' => $data['client']['id'],
                    'montant' => $data['client']['dette']['montant'],
                    // 'created_at' => $data['dette']['date'],
                ]);
    
                // Restaurer les paiements associés
                // foreach ($data['dette']['paiements'] as $paiement) {
                //     Paiement::create([
                //         'dette_id' => $restoredDette->id,
                //         'montant' => $paiement['montant'],
                //         'created_at' => $paiement['date'],
                //     ]);
                // }
    
                // Restaurer les articles associés
                // foreach ($data['dette']['articles'] as $article) {
                //     $articleModel = Article::find($article['id']);
                //     if ($articleModel) {
                //         $restoredDette->articles()->attach($articleModel->id, [
                //             'qte_vente' => $article['qte'],
                //             'prix_vente' => $article['prix_unitaire'],
                //         ]);
                //     }
                // }
    
                // Supprimer la dette restaurée de l'archive MongoDB
                //$collection->deleteOne(['dette_id' => new ObjectId($dette['_id'])]);
    
                $restoredCount++;
            }
    
            // Retourner le nombre de dettes restaurées
            return response()->json([
                'success' => true,
                'message' => "$restoredCount dettes restaurées avec succès",
            ], 200);
        } catch (\Exception $e) {
            // Enregistrer l'erreur et retourner une réponse d'erreur
            Log::error('Erreur lors de la restauration des dettes depuis MongoDB : ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la restauration des dettes',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    public function restoreArchivedDettesByClient($clientId)
    {
        try {
            // Sélectionner la collection 'archivage_dette' dans MongoDB
            $collection = $this->mongoClient->selectCollection($this->databaseName, 'archivage_dette');
    
            // Rechercher les dettes archivées pour un client spécifique
            $dettesCursor = $collection->find([
                'client_id' => (int)$clientId // Assurer que client_id est un entier
            ]);
    
            // Vérifier si des dettes ont été trouvées
            $restoredCount = 0;
            foreach ($dettesCursor as $dette) {
                $archived_at = isset($dette['archived_at']) ? $dette['archived_at'] : now();
    
                // Vérifier si le client existe dans PostgreSQL
                $client = Client::find($clientId);
                if (!$client) {
                    throw new \Exception('Client non trouvé dans PostgreSQL pour la dette ID: ' . $dette['_id']);
                }
    
                // Calculer la somme des paiements
                $sommePaiements = 0;
                if (isset($dette['paiements']) && !empty($dette['paiements'])) {
                    foreach ($dette['paiements'] as $paiement) {
                        $sommePaiements += isset($paiement['montant']) ? (float)$paiement['montant'] : 0;
                    }
                }
    
                // Affecter la somme des paiements à la clé montant
                $montant = $sommePaiements;
    
                // Restaurer la dette dans PostgreSQL avec le montant basé sur les paiements
                $restoredDette = Dette::create([
                    'client_id' => $dette['client_id'],
                    'montant' => $montant,
                    'created_at' => $archived_at,  // Utiliser la date d'archivage comme date de création
                ]);
    
                // Restaurer les paiements associés
                // if (isset($dette['paiements']) && !empty($dette['paiements'])) {
                //     foreach ($dette['paiements'] as $paiement) {
                //         Paiement::create([
                //             'dette_id' => $restoredDette->id,
                //             'montant' => $paiement['montant'],
                //             'created_at' => $paiement['created_at'],
                //         ]);
                //     }
                // }
    
                // Restaurer les articles associés
                // if (isset($dette['articles']) && !empty($dette['articles'])) {
                //     foreach ($dette['articles'] as $article) {
                //         $articleModel = Article::find($article['pivot']['article_id']);
                //         if ($articleModel) {
                //             $restoredDette->articles()->attach($articleModel->id, [
                //                 'qte_vente' => $article['pivot']['qte_vente'],
                //                 'prix_vente' => $article['pivot']['prix_vente'],
                //             ]);
                //         }
                //     }
                // }
    
                // Supprimer la dette de l'archive MongoDB après restauration
                //$collection->deleteOne(['_id' => new ObjectId($dette['_id'])]);
    
                $restoredCount++;
            }
    
            // Retourner le nombre de dettes restaurées
            return response()->json([
                'success' => true,
                'message' => "$restoredCount dettes restaurées avec succès",
            ], 200);
        } catch (\Exception $e) {
            // Enregistrer l'erreur et retourner une réponse d'erreur
            Log::error('Erreur lors de la restauration des dettes depuis MongoDB : ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la restauration des dettes',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    
    
    public function getSoldesDettes()
    {
        Log::info('recup_mongo');
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
