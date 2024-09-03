<?php
namespace App\Repository;;

use App\Models\Article;
use App\Repository\ArticleRepository ;

class ArticleRepositoryImpl implements ArticleRepository
{
    protected $model;

    public function __construct(Article $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    // public function update($id, array $data)
    // {
    //     return $this->model->where('id', $id)->update($data);
    // }

    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    // public function findByLibelle($libelle)
    // {
    //     return $this->model->where('libelle', $libelle)->get();
    // }
    public function findByLibelle($libelle)
    {
        return $this->model->libelle($libelle)->get();

    }
    public function findByEtat($etat)
    {
        return $this->model->where('etat', $etat)->get();
    }

    public function filter(array $filters)
    {
        return $this->model->filter($filters);
    }

    public function update(int $id, array $data)
    {
        $article = $this->model->find($id);

        if (!$article) {
            return null;
        }

        $article->update($data);

        return $article;
    }
}

