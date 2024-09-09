<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'articles';

    protected $fillable = ['libelle', 'qte', 'prix_unitaire'];

    protected $guarded = ['id'];

    protected $hidden = ['id', 'created_at', 'updated_at'];

    protected $dates = ['deleted_at'];
    public function dettes()
    {
        return $this->belongsToMany(Dette::class, 'dette_article')
                    ->withPivot('qte_vente', 'prix_vente')
                    ->withTimestamps();
    }
    // Exemple de méthode filter
    // public function filter(array $filters)
    // {
    //     $query = $this->newQuery();

    //     if (isset($filters['libelle'])) {
    //         $query->where('libelle', 'LIKE', '%' . $filters['libelle'] . '%');
    //     }

    //     if (isset($filters['qte'])) {
    //         $query->where('qte', $filters['qte']);
    //     }

    //     if (isset($filters['prix_unitaire'])) {
    //         $query->where('prix_unitaire', $filters['prix_unitaire']);
    //     }

    //     // Ajoutez d'autres filtres selon les besoins

    //     return $query->get();
    // }

    public function scopeLibelle($query, $libelle)
    {
        return $query->where('libelle', $libelle);
    }
}

