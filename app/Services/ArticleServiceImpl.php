<?php
namespace App\Services;

use App\Repository\ArticleRepository;
use App\Services\ArticleService;

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

    public function update($id, array $data)
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
    public function updateArticle(int $id, array $data): array
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
            $updatedArticle = $this->repository->update($id, $data);

            return [
                'success' => true,
                'article' => $updatedArticle
            ];
        } catch (\Exception $e) {
            // Log l'erreur si nécessaire
            // \log::error('Erreur lors de la mise à jour de l\'article: ' . $e->getMessage());

            return [
                'success' => false,
                'status' => 500,
                'message' => 'Erreur lors de la mise à jour de l\'article.'
            ];
        }
    }

    public function updateArticleQuantities(array $articlesData): array
    {
        $failedUpdates = [];
        $successfulUpdates = [];

        foreach ($articlesData as $articleData) {
            $article = $this->repository->find($articleData['id']);
            if ($article) {
                try {
                    // Augmenter la quantité
                    $newQte = $article->qte + $articleData['qte'];
                    $updatedArticle = $this->repository->update($article->id, ['qte' => $newQte]);

                    $successfulUpdates[] = [
                        'id' => $updatedArticle->id,
                        'qte' => $updatedArticle->qte
                    ];
                } catch (\Exception $e) {
                    // \Log::error('Erreur lors de la mise à jour de l\'article ID ' . $article->id . ': ' . $e->getMessage());
                    $failedUpdates[] = [
                        'id' => $articleData['id'],
                        'qte' => $articleData['qte']
                    ];
                }
            } else {
                $failedUpdates[] = [
                    'id' => $articleData['id'],
                    'qte' => $articleData['qte']
                ];
            }
        }

        // Préparer la réponse
        $response = [
            'message' => 'Les articles ont été mis à jour avec succès.',
            'successful_updates' => $successfulUpdates,
            'failed_updates' => $failedUpdates,
        ];

        if (!empty($failedUpdates)) {
            $response['message'] = 'Certaines mises à jour ont échoué.';
        }

        return $response;
    }
}
