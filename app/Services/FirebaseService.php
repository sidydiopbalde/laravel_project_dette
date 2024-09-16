<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Client;
use App\Models\Dette;
use App\Models\Paiement;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;
use Google\Cloud\Firestore\FirestoreClient;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

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
       
        // Nommer la collection par date
        $collectionName = now()->format('Y-m-d');
        $client = Client::find($dette->client_id);
     
        // Préparer les données à archiver
        $data = [
            'client' => [
                'id' => $client->id,
                'nom' => $client->surnom,
                'prenom' => $client->telephone,
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
        $dette->delete();
        return true;
    }


  
    public function getArchivedDettes($filter = [])
    {
        try {
            // Obtenir la référence à la racine de Firebase
            $rootReference = $this->database->getReference('/');
            
            // Récupérer toutes les collections (qui sont nommées par date)
            $collectionsSnapshot = $rootReference->getSnapshot();
            $collections = $collectionsSnapshot->getValue();
    
            $allDettes = [];
    
            // Filtrage par date
            if (isset($filter['date'])) {
                // Si la date est fournie, on ne récupère que la collection de cette date
                $collectionName = $filter['date']; // Exemple : '2024-09-13'
                
                if (isset($collections[$collectionName])) {
                    // Récupérer la collection pour la date spécifiée
                    $reference = $this->database->getReference($collectionName);
    
                    // Filtrer par client_id si fourni
                    if (isset($filter['client_id'])) {
                        $query = $reference->orderByChild('client/id')->equalTo((int)$filter['client_id']);
                    } else {
                        $query = $reference; // Récupérer tout si pas de filtre client
                    }
    
                    // Récupérer les dettes archivées pour cette collection
                    $snapshot = $query->getSnapshot();
                    $archivedDettes = $snapshot->getValue();
    
                    // Ajouter les résultats à la liste des dettes
                    if ($archivedDettes) {
                        $allDettes = array_merge($allDettes, $archivedDettes);
                    }
                }
            } else {
                // Si aucune date n'est fournie, on récupère toutes les collections
                foreach ($collections as $collectionName => $collectionData) {
                    $reference = $this->database->getReference($collectionName);
    
                    // Filtrer par client_id si fourni
                    if (isset($filter['client_id'])) {
                        $query = $reference->orderByChild('client/id')->equalTo($filter['client_id']);
                        // dd($query);
                    } else {
                        $query = $reference; // Récupérer tout si pas de filtre client
                    }
    
                    // Récupérer les dettes archivées pour chaque collection
                    $snapshot = $query->getSnapshot();
                    $archivedDettes = $snapshot->getValue();
    
                    // Ajouter les résultats à la liste des dettes
                    if ($archivedDettes) {
                        $allDettes = array_merge($allDettes, $archivedDettes);
                    }
                }
            }
    
            return $allDettes;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des dettes archivées depuis Firebase : ' . $e->getMessage());
            return [];
        }
    }
    
    
    
    public function getArchivedDettesByClient($clientId)
    {
        try {
            $rootReference = $this->database->getReference('/');
            // dd($rootReference);
            $collectionsSnapshot = $rootReference->getSnapshot();
            $collections = $collectionsSnapshot->getValue();
            $allDettes = [];
            
            foreach ($collections as $collectionName => $collectionData) {
                $reference = $this->database->getReference($collectionName);
                $query = $reference->orderByChild('client/id')->equalTo((int)$clientId);
                
                $snapshot = $query->getSnapshot();
                $archivedDettes = $snapshot->getValue();
                // dd($archivedDettes);

                if ($archivedDettes) {
                    $allDettes = array_merge($allDettes, $archivedDettes);
                }
            }

            return $allDettes;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des dettes archivées depuis Firebase : ' . $e->getMessage());
            return [];
        }
    }
    
    public function getArchivedDetteDetailsById($detteId)
    {
        try {
            // Accéder à la racine de Firebase
            $rootReference = $this->database->getReference('/');

            // Obtenir un snapshot de toutes les collections à la racine (les dates)
            $collectionsSnapshot = $rootReference->getSnapshot();

            // Récupérer toutes les collections (noms des dates)
            $collections = $collectionsSnapshot->getValue();

            $filteredDettes = [];

            // Itérer sur chaque collection (chaque date)
            foreach ($collections as $collectionName => $collectionData) {
                // Itérer sur chaque document dans la collection (chaque dette)
                foreach ($collectionData as $documentId => $documentData) {
                    // Vérifier si l'id de la dette correspond au filtre
                    if (isset($documentData['client']['dette']['id']) && $documentData['client']['dette']['id'] == $detteId) {
                        $filteredDettes[] = $documentData['client']['dette'];
                    }
                }
            }

            return $filteredDettes;
        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des détails de la dette depuis Firebase : ' . $e->getMessage());
            return [];
        }
    }

    
    public function restoreArchivedDettesByDate($date)
{
    try {
        // Accéder à la racine de Firebase
        $rootReference = $this->database->getReference('/');

        // Obtenir un snapshot de toutes les collections à la racine (les dates)
        $collectionsSnapshot = $rootReference->getSnapshot();

        // Récupérer toutes les collections (noms des dates)
        $collections = $collectionsSnapshot->getValue();

        // Vérifier si la date spécifiée existe comme nom de collection
        if (!isset($collections[$date])) {
            throw new \Exception('Aucune dette trouvée pour cette date');
        }

        // Référence à la collection pour la date spécifiée
        $collectionReference = $this->database->getReference($date);
        $dettesSnapshot = $collectionReference->getSnapshot();
        $dettes = $dettesSnapshot->getValue();

        if (empty($dettes)) {
            throw new \Exception('Aucune dette trouvée pour cette date');
        }

        $restoredCount = 0;
        foreach ($dettes as $key => $dette) {
            // Assurez-vous que la structure de la dette est correcte
            if (!isset($dette['client']['dette'])) {
                throw new \Exception('Donnée de dette mal formée pour la clé : ' . $key);
            }

            $data = $dette['client']['dette'];
            $client_id = (int)$dette['client']['id'];

            // Vérifier si le client existe dans PostgreSQL
            $client = Client::find($client_id);
            if (!$client) {
                throw new \Exception('Client non trouvé dans PostgreSQL pour la dette ID: ' . $data['id']);
            }
            
            // Restaurer la dette dans PostgreSQL
            $restoredDette = Dette::create([
                'client_id' => $client_id,
                'montant' => $data['montant'],
                'created_at' => $data['date'],
            ]);
            
            // dd($restoredDette);
            // Restaurer les paiements associés
            // foreach ($data['paiements'] as $paiement) {
            //     Paiement::create([
            //         'dette_id' => $restoredDette->id,
            //         'montant' => $paiement['montant'],
            //         'created_at' => $paiement['date'],
            //     ]);
            // }

            // Restaurer les articles associés
            // foreach ($data['articles'] as $article) {
            //     $articleModel = Article::find($article['id']);
            //     if ($articleModel) {
            //         $restoredDette->articles()->attach($articleModel->id, [
            //             'qte_vente' => $article['qte'],
            //             'prix_vente' => $article['prix_unitaire'],
            //         ]);
            //     }
            // }

            // Supprimer la dette de Firebase après restauration
            // $this->database->getReference($date . '/' . $key)->remove();

            $restoredCount++;
        }

        // Retourner le nombre de dettes restaurées
        return response()->json([
            'success' => true,
            'message' => "$restoredCount dettes restaurées avec succès",
        ], 200);
    } catch (\Exception $e) {
        // Enregistrer l'erreur et retourner une réponse d'erreur
        Log::error('Erreur lors de la restauration des dettes depuis Firebase : ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la restauration des dettes',
            'details' => $e->getMessage(),
        ], 500);
    }
}

    

public function restoreArchivedDetteById($detteId)
{
    try {
        // Récupérer la référence Firebase root
        $rootReference = $this->database->getReference('/');
        $collectionsSnapshot = $rootReference->getSnapshot();
        $collections = $collectionsSnapshot->getValue();
        $detteData = null;

        // Parcourir les collections pour trouver la dette correspondante
        foreach ($collections as $collectionName => $collectionData) {
            foreach ($collectionData as $documentId => $documentData) {
                if (isset($documentData['client']['dette']['id']) && $documentData['client']['dette']['id'] == (int)$detteId) {
                    $detteData = $documentData;
                    $documentKey = $documentId; // Stocker l'ID du document pour la suppression
                    break 2; // Quitter les boucles une fois la dette trouvée
                }
            }
        }
        // Si aucune dette n'a été trouvée
        if (!$detteData) {
            return response()->json([
                'success' => false,
                'message' => 'Dette non trouvée dans les archives',
            ], 404);
        }
        
        // Vérifier si le client existe dans PostgreSQL
        $client = Client::find($detteData['client']['id']);
        if (!$client) {
            throw new \Exception('Client non trouvé dans PostgreSQL pour la dette ID: ' . $detteId);
        }
        
        // Restaurer la dette dans PostgreSQL
        $restoredDette = Dette::create([
            'client_id' => $detteData['client']['id'],
            'montant' => $detteData['client']['dette']['montant'],
            // 'created_at' => $detteData['client']['dette']['date'],
        ]);
        // dd($restoredDette);

        // Restaurer les paiements associés (si présents)
        // foreach ($detteData['client']['dette']['paiements'] ?? [] as $paiement) {
        //     Paiement::create([
        //         'dette_id' => $restoredDette->id,
        //         'montant' => $paiement['montant'],
        //         'created_at' => $paiement['date'],
        //     ]);
        // }

        // Restaurer les articles associés (si présents)
        // foreach ($detteData['client']['dette']['articles'] ?? [] as $article) {
        //     $articleModel = Article::find($article['id']);
        //     if ($articleModel) {
        //         $restoredDette->articles()->attach($articleModel->id, [
        //             'qte_vente' => $article['qte'],
        //             'prix_vente' => $article['prix_unitaire'],
        //         ]);
        //     }
        // }

        // Supprimer la dette des archives Firebase après la restauration
       // $this->database->getReference("$collectionName/$documentKey")->remove();

        return response()->json([
            'success' => true,
            'message' => 'Dette restaurée avec succès',
        ], 200);
    } catch (\Exception $e) {
        // Enregistrer l'erreur dans les logs et retourner une réponse d'erreur
        Log::error('Erreur lors de la restauration de la dette depuis Firebase : ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Erreur lors de la restauration de la dette',
            'details' => $e->getMessage(),
        ], 500);
    }
}


        
    public function restoreArchivedDettesByClient($clientId)
    {
        try {
        
                $rootReference = $this->database->getReference('/');
                $collectionsSnapshot = $rootReference->getSnapshot();
                $collections = $collectionsSnapshot->getValue();
                foreach ($collections as $collectionName => $collectionData) {
                    $reference = $this->database->getReference($collectionName);
                    $query = $reference->orderByChild('client/id')->equalTo((int)$clientId);
                    
                    $snapshot = $query->getSnapshot();
                    $archivedDettes = $snapshot->getValue();
                }
                if (empty($archivedDettes)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Aucune dette trouvée pour ce client',
                    ], 404);
                }
                
                $restoredCount = 0;
                
                // Utiliser une transaction pour optimiser les insertions
                DB::transaction(function () use (&$archivedDettes, &$restoredCount) {
                    foreach ($archivedDettes as $key => $dette) {
                        $data = $dette['client']['dette'];
                        
                        // Vérifier si le client existe dans PostgreSQL
                        $client = Client::find((int)$dette['client']['id']);
                        if (!$client) {
                            throw new \Exception('Client non trouvé dans PostgreSQL pour la dette ID: ' . $data['dette']['id']);
                        }
                        
                        // Restaurer la dette dans PostgreSQL
                        $restoredDette = Dette::create([
                            'client_id' => $dette['client']['id'],
                            'montant' => $data['montant'],
                            // 'created_at' => $data['dette']['date'],
                        ]);
                        // dd($restoredDette);

                    // Restaurer les paiements associés
                    // if (!empty($data['dette']['paiements'])) {
                    //     foreach ($data['dette']['paiements'] as $paiement) {
                    //         Paiement::create([
                    //             'dette_id' => $restoredDette->id,
                    //             'montant' => $paiement['montant'],
                    //             'created_at' => $paiement['date'],
                    //         ]);
                    //     }
                    // }

                    // Restaurer les articles associés
                    // if (!empty($data['dette']['articles'])) {
                    //     foreach ($data['dette']['articles'] as $article) {
                    //         $articleModel = Article::find($article['id']);
                    //         if ($articleModel) {
                    //             $restoredDette->articles()->attach($articleModel->id, [
                    //                 'qte_vente' => $article['qte'],
                    //                 'prix_vente' => $article['prix_unitaire'],
                    //             ]);
                    //         }
                    //     }
                    // }

                    // Supprimer la dette de Firebase après restauration
                // $this->database->getReference('archivage_dette/' . $key)->remove();

                    $restoredCount++;
                }
            });

            // Retourner le nombre de dettes restaurées
            return response()->json([
                'success' => true,
                'message' => "$restoredCount dettes restaurées avec succès",
            ], 200);
        } catch (\Exception $e) {
            // Enregistrer l'erreur et retourner une réponse d'erreur
            Log::error('Erreur lors de la restauration des dettes depuis Firebase : ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la restauration des dettes',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

}