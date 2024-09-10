<?php

namespace App\Services;

use App\Exceptions\ServiceException;
use App\Models\Article;

use App\Models\Paiement;
use App\Repository\DetteRepository;
use App\Facades\DetteRepositoryFacade;
use App\Models\Client;
use App\Models\Dette;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DetteServiceImpl implements DetteService
{
    public function getArticlesByDetteId(int $id)
    {
        // Utilise le repository pour récupérer les articles
        return DetteRepositoryFacade::findArticlesByDetteId($id);
    }
    public function getPaiementsByDetteId(int $id)
    {
        // Utilise le repository pour récupérer les paiements
        return DetteRepositoryFacade::findPaiementsByDetteId($id);
    }
    public function getClientDettes(int $clientId)
    {
        // Récupérer les informations du client
        $client = Client::find($clientId);

        // Vérifier si le client existe
        if (!$client) {
            throw new ServiceException('Client non trouvé.');
        }

        // Récupérer les dettes du client via le repository
        $dettes = DetteRepositoryFacade::getDettesByClientId($clientId);

        // Retourner les informations du client et ses dettes, ou null si pas de dettes
        return [
            'client' => [
                'surnom' => $client->surnom,
                'telephone' => $client->telephone,
                'dettes' => $dettes->isNotEmpty() ? $dettes : null,
            ]
        ];
    }
    public function getDetteById($id)
    {
        return DetteRepositoryFacade::findById($id);
    }
   public function createDette(array $data)
    {
        // Démarrer une transaction pour garantir l'intégrité des données
        DB::beginTransaction();
        
        try {
            // Créer la dette
            $dette = DetteRepositoryFacade::create([
                'montant' => $data['montant'],
                'client_id' => $data['clientId'],
            ]);

            // Ajouter les articles associés à la dette et mettre à jour les stocks
            foreach ($data['articles'] as $articleData) {
                // Attacher les articles à la dette via la table pivot
                $dette->articles()->attach($articleData['articleId'], [
                    'qte_vente' => $articleData['qte'],
                    'prix_vente' => $articleData['prix_unitaire'],
                ]);

                // Récupérer l'article pour mettre à jour le stock
                $article = Article::find($articleData['articleId']);

                // Vérifier s'il y a suffisamment de stock
                if ($article->qte >= $articleData['qte']) {
                    // Mettre à jour le stock de l'article
                    $article->qte -= $articleData['qte'];
                    $article->save();
                } else {
                    throw new \Exception('Stock insuffisant pour l\'article: ' . $article->libelle);
                }
            }

            // Ajouter les informations de paiement si présentes
            if (isset($data['paiement']['montant'])) {

                if ($data['paiement']['montant'] > $dette->montant) {
                    throw new \Exception('Le montant du paiement ne peut pas être supérieur au montant de la dette.');
                }


                Paiement::create([
                    'dette_id' => $dette->id,
                    'montant' => $data['paiement']['montant'],
                ]);
            }

            // Commit la transaction pour valider toutes les opérations
            DB::commit();
    // Charger les relations client et articles
            $dette->load('client', 'articles');

            // Retourner la réponse avec les données et le statut 201
            return response()->json([
                'message' => 'Dette enregistrée avec succès',
                'data' => [
                    'dette' => $dette,
                    'client' => $dette->client,
                    'articles' => $dette->articles,
                ]
            ], 201);
            // return $dette;
        } catch (\Exception $e) {
            // En cas d'erreur, rollback la transaction
            DB::rollBack();
            throw $e;
        }
    }
public function addArticlesToDette(array $articlesData, int $detteId)
{
    // Démarrer une transaction pour garantir l'intégrité des données
    DB::beginTransaction();

    try {
        $dette = Dette::findOrFail($detteId);

        foreach ($articlesData as $articleData) {
            $article = Article::find($articleData['articleId']);
            
            if ($article && $article->qte >= $articleData['qte']) {
                // Attacher les articles
                $dette->articles()->attach($articleData['articleId'], [
                    'qte_vente' => $articleData['qte'],
                    'prix_vente' => $articleData['prix_unitaire'],
                ]);
                
                // Mise à jour du stock
                $article->qte -= $articleData['qte'];
                $article->save();
            } else {
                throw new \Exception('Stock insuffisant pour l\'article: ' . $article->libelle);
            }
        }

        // Commit la transaction
        DB::commit();

        return $dette->load('articles');
    } catch (\Exception $e) {
        DB::rollBack();
        throw $e;
    }
}

    // public function createDebt(array $data)
    // {
    //     return $this->debtRepository->create($data);
    // }

    // public function updateDebt(int $id, array $data)
    // {
    //     return $this->debtRepository->update($id, $data);
    // }

    // public function deleteDebt(int $id)
    // {
    //     return $this->debtRepository->delete($id);
    // }

    // public function getDebtById(int $id)
    // {
    //     return $this->debtRepository->findById($id);
    // }

    // public function getDebtsByClient(int $clientId)
    // {
    //     return $this->debtRepository->findByClient($clientId);
    // }
}
