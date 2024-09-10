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
    public function scopeDisponible($query, $isAvailable = true)
    {
        if ($isAvailable) {
            return $query->where('qte', '>', 0); // Articles disponibles
        } else {
            return $query->where('qte', '=', 0); // Articles non disponibles
        }
    }

    public function scopeLibelle($query, $libelle)
    {
        return $query->where('libelle', 'like', '%' . $libelle . '%');
    }
    // public function scopeLibelle($query, $libelle)
    // {
    //     return $query->where('libelle', $libelle);
    // }
}

