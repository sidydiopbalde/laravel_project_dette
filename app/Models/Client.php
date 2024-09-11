<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Scopes\FilterScope;
 use App\Observers\ClientObserver;
 use Illuminate\Database\Eloquent\Attributes\ObservedBy;
 #[ObservedBy([ClientObserver::class])]
class Client extends Model
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
     protected $hidden=['id', 'created_at', 'updated_at'];
     protected $appends = ['photo'];
     protected static function booted()
     {
         // Ajouter le scope global
         static::addGlobalScope(new FilterScope(request()->all()));
        //  static::observe(ClientObserver::class);
     }
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

     public function getPhotoAttribute()
    {
        // Si un utilisateur est associé, ne pas afficher l'attribut 'photo'
        if (!is_null($this->user_id)) {
            return null; // Pas d'attribut 'photo' pour les clients ayant un user_id
        }

        // Retourner la valeur de la photo par défaut si aucun utilisateur n'est associé
        return 'app/public/photos/MD2m5haBKOnj0qnYYRwhhDiR821WQfaMi7mgHk0P.png';  // Remplacez par le chemin de la photo par défaut
    }
}

