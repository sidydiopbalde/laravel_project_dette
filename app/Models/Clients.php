<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clients extends Model
{
    use HasFactory;

    // Si vous utilisez des noms de table personnalisés ou des clés primaires autres que 'id', définissez-les ici
    // protected $table = 'clients';
    // protected $primaryKey = 'client_id';

    // Définissez les attributs que vous pouvez remplir en masse
    protected $fillable = [
        'surnom',
        'adresse',
        'telephone',
    ];

     // Définir les attributs qui ne peuvent pas être assignés en masse
     protected $guarded = ['id', 'created_at', 'updated_at'];

    // Définir la relation avec le modèle UserProfile
    public function user()
    {
        return $this->belongsTo(Users::class, 'user_id');
    }
      // Relation avec les dettes
      public function dettes()
      {
          return $this->hasMany(Dette::class);
      }
}

