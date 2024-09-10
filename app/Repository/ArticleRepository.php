<?php
namespace App\Repository;

interface ArticleRepository
{
    public function all();
    public function create(array $data);
    public function find($id);
    // public function update($id, array $data);
    public function delete($id);
    public function findByLibelle($libelle);
    public function findByEtat($etat);
    public function filter(array $filters);  // Ajout de la méthode filter


      /**
     * Mettre à jour un article spécifique.
     *
     * @param int $id
     * @param array $data
     * @return \App\Models\Article|null
     */
    public function update(int $id, int $data);
}
