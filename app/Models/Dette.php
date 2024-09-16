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

    protected $fillable = ['client_id', 'montant', 'date_echeance'];
    protected $hidden=['id','created_at','updated_at'];
    protected $appends=['montant_verse','montant_du'];
    public function client()
    {
        return $this->belongsTo(Client::class);

    }
    // Relation avec Paiements
    public function paiements()
    {
        return $this->hasMany(Paiement::class,'dette_id');
    }

    public function articles()
    {
        return $this->belongsToMany(Article::class, 'dette_article')
                    ->withPivot('qte_vente', 'prix_vente')
                    ->withTimestamps();
    }
  
    public function scopeStatut($query, $statut)
    {
        if ($statut === 'Solde') {
            return $query->whereRaw('(SELECT COALESCE(SUM(montant), 0) FROM paiements WHERE paiements.dette_id = dettes.id) >= dettes.montant');
        } elseif ($statut === 'NonSolde') {
            return $query->whereRaw('(SELECT COALESCE(SUM(montant), 0) FROM paiements WHERE paiements.dette_id = dettes.id) < dettes.montant');
        }
        
        return $query;
    }

        public function getMontantVerseAttribute()
        {
            // Exemple de calcul : additionner tous les paiements liés à cette dette
            $this->montant_verse=$this->paiements()->sum('montant');
            return $this->montant_verse;
        }

    /**
     * Calculer le montant dû pour cette dette.
     *
     * @return float
     */
    public function getMontantDuAttribute()
    {
        // Exemple de calcul : Montant total - Montant versé
        $this->montant_du=$this->montant - $this->montant_verse;
        return $this->montant_du;
    }

    public function montantRestant()
    {
        $totalPaiements = $this->paiements->sum('montant'); // Ajustez le nom de la colonne si nécessaire
        return $this->montant_total - $totalPaiements;
    }
}
