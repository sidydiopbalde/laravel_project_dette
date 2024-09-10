<?php
namespace App\Services;

interface ArticleService
{
    public function all();
    public function create(array $data);
    public function find($id);
    public function update($id, int $data);
    public function delete($id);
    public function findByLibelle($libelle);
    public function findByEtat($etat);
    public function updateArticle(int $id, int $data): array;
    public function updateArticleQuantities(array $articlesData): array;
}
