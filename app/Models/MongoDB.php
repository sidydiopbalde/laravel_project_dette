<?php

namespace App\Models;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Jenssegers\Mongodb\Eloquent\Model ;

class ArchivedDette extends EloquentModel
{
    // Indique que le modèle utilise la connexion MongoDB
    protected $connection = 'mongodb';

    // Indique la collection MongoDB utilisée
    protected $collection = 'archived_dettes';

    // Définir les champs du modèle
    protected $fillable = [
        'client',
        'dette',
        'articles',
        'paiements',
    ];

    /**
     * Relation avec le client
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client.id', 'id');
    }

    /**
     * Restaurer la dette depuis MongoDB
     */
    public function restoreToPostgres()
    {
        // Récupérer le client
        $client = Client::find($this->client['id']);
        if (!$client) {
            throw new \Exception('Client non trouvé dans PostgreSQL pour la dette ID: ' . $this->dette['id']);
        }

        // Créer la dette dans PostgreSQL
        $restoredDette = Dette::create([
            'client_id' => $this->client['id'],
            'montant' => $this->dette['montant'],
            'created_at' => $this->dette['date'],
        ]);

        // Restaurer les paiements
        foreach ($this->paiements ?? [] as $paiement) {
            Paiement::create([
                'dette_id' => $restoredDette->id,
                'montant' => $paiement['montant'],
                'created_at' => $paiement['date'],
            ]);
        }

        // Restaurer les articles
        foreach ($this->articles ?? [] as $article) {
            $articleModel = Article::find($article['id']);
            if ($articleModel) {
                $restoredDette->articles()->attach($articleModel->id, [
                    'qte_vente' => $article['qte'],
                    'prix_vente' => $article['prix_unitaire'],
                ]);
            }
        }

        // Supprimer la dette de MongoDB après restauration
        $this->delete();

        return $restoredDette;
    }
}

