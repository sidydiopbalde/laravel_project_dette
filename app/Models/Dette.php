<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Dette",
 *     type="object",
 *     title="Dette",
 *     required={"client_id", "montant", "montant_due", "date"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="client_id", type="integer", example=1),
 *     @OA\Property(property="montant", type="number", format="float", example=150.75),
 *     @OA\Property(property="montant_due", type="number", format="float", example=150.75),
 *     @OA\Property(property="date", type="string", format="date", example="2023-09-01"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-09-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-09-01T00:00:00Z")
 * )
 */

class Dette extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'montant'];
    protected $hidden=['id','created_at','updated_at'];
    public function client()
    {
        return $this->belongsTo(Clients::class);

    }
    // Relation avec Paiements
    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'dette_article')
                    ->withPivot('qte_vente', 'prix_vente')
                    ->withTimestamps();
    }
      // Scope local pour filtrer les dettes soldées ou non soldées
      public function scopeStatut($query, $statut)
      {
          return $query->where(function ($q) use ($statut) {
              // Calcul de la somme des paiements pour chaque dette
              $q->whereRaw('(SELECT SUM(montant) FROM paiements WHERE paiements.dette_id = dettes.id) ' .
                           ($statut == 'Solde' ? '>=' : '<') . ' dettes.montant');
          });
      }
}
