<?php
namespace App\Services;

use App\Repository\ArticleRepository;
use App\Services\ArticleService;
use App\Exceptions\ServiceException;
class ArticleServiceImpl implements ArticleService
{
    protected $repository;

    public function __construct(ArticleRepository $repository)
    {
        $this->repository = $repository;
    }

    public function all()
    {
        return $this->repository->all();
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function find($id)
    {
        return $this->repository->find($id);
    }

    public function update($id, int $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function findByLibelle($libelle)
    {
        return $this->repository->findByLibelle($libelle);
    }

    public function findByEtat($etat)
    {
        return $this->repository->findByEtat($etat);
    }

    public function filter(array $filters)
    {
        return $this->repository->filter($filters);
    }


     /**
     * Mettre à jour un article spécifique.
     *
     * @param int $id
     * @param array $data
     * @return array
     */
    public function updateArticle(int $id, int $data): array
    {
        // Récupérer l'article existant
        $article = $this->repository->find($id);

        if (!$article) {
            return [
                'success' => false,
                'status' => 404,
                'message' => 'Article non trouvé.'
            ];
        }

        try {
            // Vérifier si 'qte' est présent dans les données et l'augmenter
            if (isset($data['qte'])) {
                $data['qte'] = $article->qte + $data['qte'];
            }

            // Mettre à jour l'article via le repository
            $updatedArticle = $this->repository->update($id, $data['qte']);

            return [
                'success' => true,
                'article' => $updatedArticle
            ];
        } catch (\Exception $e) {
            // Log l'erreur si nécessaire
            throw new ServiceException('Erreur lors de la mise à jour de l\'article: ' . $e->getMessage());

            return [
                'success' => false,
                'status' => 500,
                'message' => 'Erreur lors de la mise à jour de l\'article.'
            ];
        }
    }

    // public function updateQuantities(Request $request)
    // {
    //     // $this->authorize('create', Article::class); 
    //     $articles = $request->input('articles', []);
    //     $articlesWithErrors = [];
    
    //     foreach ($articles as $articleData) {
    //         $articleId = $articleData['articleId'] ?? null;
    //         $quantity = $articleData['quantity'] ?? null;
    
    //         if (is_null($articleId) || is_null($quantity)) {
    //             $articlesWithErrors[] = [
    //                 'articleId' => $articleId,
    //                 'quantity' => $quantity,
    //                 'error' => 'Données manquantes',
    //             ];
    //             continue;
    //         }
    
    //         $article = $this->articleService->find($articleId);
    
    //         if ($article) {
    //             if ($quantity < 0) {
    //                 $articlesWithErrors[] = [
    //                     'article' => $article,
    //                     'quantity' => $quantity,
    //                     'error' => 'Quantité invalide',
    //                 ];
    //             } else {
    //                 $updatedArticle = $this->articleService->update($articleId, [
    //                     'qutestock' => $article->qutestock + $quantity
    //                 ]);
    //             }
    //         } else {
    //             $articlesWithErrors[] = [
    //                 'articleId' => $articleId,
    //                 'quantity' => $quantity,
    //                 'error' => 'Article non trouvé',
    //             ];
    //         }
    //     }
    
    //     if (count($articlesWithErrors) > 0) {
    //         return [
    //             'message' => 'Certaines mises à jour ont échoué.',
    //             'errors' => $articlesWithErrors,
    //         ];
    //     }
    
    //     return response()->json([
    //         'message' => 'Tous les articles ont été mis à jour avec succès.',
    //     ], 200);
    // }
    
    public function updateArticleQuantities(array $articlesData): array
    {
        $failedUpdates = [];
        $successfulUpdates = [];
        
        foreach ($articlesData as $articleData) {
            $articleId = $articleData['id'] ?? null;
            $quantity = $articleData['qte'] ?? null;
            
            // Vérifier si l'ID ou la quantité sont manquants
            if (is_null($articleId) || is_null($quantity)) {
                $failedUpdates[] = [
                    'article' => $articleData,  // Inclure l'objet complet si les données sont manquantes
                    'error' => 'Données manquantes'
                ];
                continue;
            }
            
            $article = $this->repository->find($articleId);
            
            if ($article) {
                if ($quantity < 0) {
                    // Si la quantité est invalide, inclure l'objet article et l'erreur
                    $failedUpdates[] = [
                        'article' => $article,
                        'quantity' => $quantity,
                        'error' => 'Quantité invalide'
                    ];
                } else {
                    // Mettre à jour la quantité
                    $newQte = $article->qte + $quantity;
                    // dd($newQte);
                    $updatedArticle = $this->repository->update($article->id, $newQte);
    
                    $successfulUpdates[] = [
                        'article' => $updatedArticle,  // Inclure l'objet mis à jour
                        'qte' => $updatedArticle->qte
                    ];
                }
            } else {
                // Si l'article n'est pas trouvé, inclure l'ID dans l'erreur
                $failedUpdates[] = [
                    'article' => $articleData,  // Inclure les données d'origine même si l'article est non trouvé
                    'error' => 'Article non trouvé'
                ];
            }
        }
    
        // Retourner les résultats
        return [
            'message' => count($failedUpdates) > 0 ? 'Certaines mises à jour ont échoué.' : 'Tous les articles ont été mis à jour avec succès.',
            'errors' => $failedUpdates,
            'success' => $successfulUpdates
        ];
    }
    
}    