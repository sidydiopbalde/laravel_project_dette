<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DetteResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'montant' => $this->montant,
            'client_id' => $this->client_id,
            'articles' => $this->articles->map(function ($article) {
                return [
                    'article_id' => $article->article_id,
                    'qteVente' => $article->qte_vente,
                    'prixVente' => $article->prix_vente,
                ];
            }),
            'paiement' => [
                'montant' => $this->paiement_montant,
            ],
        ];
    }
}
