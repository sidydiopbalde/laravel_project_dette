<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Article extends Model
{
    use HasFactory;
    use SoftDeletes;

    // Définissez la table si elle n'est pas le pluriel du modèle
    protected $table = 'articles';

    // Spécifiez les attributs qui peuvent être assignés en masse
    protected $fillable = ['libelle', 'qte','prix_unitaire'];
    protected $guarded = ['id', 'created_at', 'updated_at'];
    // Spécifiez la colonne pour les soft deletes (facultatif, Laravel utilise par défaut 'deleted_at')
    protected $dates = ['deleted_at'];
}
