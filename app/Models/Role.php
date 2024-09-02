<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;
    protected $fillable = ['libelle'];
    protected $hidden = ['id','created_at', 'updated_at'];
    
    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    // public function clients()
    // {
    //     return $this->hasOne(Clients::class);
    // }
    // public function boutiques()
    // {
    //     return $this->hasMany(Boutiques::class);
    // }
    // public function commandes()
    // {
    //     return $this->hasMany(Commandes::class);
    // }
    // public function articles()
    // {
    //     return $this->hasMany(Articles::class);
    // }
    // public function articles_commandes()
    // {
    //     return $this->hasMany(ArticlesCommandes::class);
    // }
    // public function stocks()
    // {
    //     return $this->hasMany(Stocks::class);
    // }
    // public function receptions()
    // {
    //     return $this->hasMany(Receptions::class);
    // }
    // public function livraisons()
    // {
    //     return $this->hasMany(Livraisons::class);
    // }
    // public function factures()
    // {
    //     return $this->hasMany(Factures::class);
    // }
    // public function paiements()
    // {
    //     return $this->hasMany(Paiements::class);
    // }
    // public function devis()
    // {
    //     return $this->hasMany(Devis::class);
    // }
    // public function factures_paiements()
    // {
    //     return $this->hasMany(FacturesPaiements::class);
    // }
    // public function factures_devis()
    // {
    //     return $this->hasMany(FacturesDevis::class);
    // }
    // public function factures_receptions()
    // {
    //     return $this->hasMany(FacturesReceptions::class);
    // }
    // public function factures_livraisons()
    // {
    //     return $this->hasMany(FacturesLivraisons::class);
    // }
    // public function factures_paiements_devis()
    // {
    //     return $this->hasMany(FacturesPaiementsDevis::class);
    // }
    // public function factures_paiements_receptions()
    // {
    //     return $this->hasMany(FacturesPaiementsReceptions::class);
    // }
    // public function factures_paiements_livraisons()
    // {
    //     return $this->hasMany(FacturesPaiementsLivraisons::class);
    // }
    // public function factures_paiements_devis_receptions()
    // {
    //     return $this->hasMany(FacturesPaiementsDevisReceptions::class);
    // }
    // public function factures_paiements_devis_livraisons()
    // {
    //     return $this->hasMany(FacturesPaiementsDevisLivraisons::class);
    // }
    // public function factures_paiements_receptions_livraisons()
    // {
    //     return $this->hasMany(FacturesPaiementsReceptionsLivraisons::class);
    // }
    // public function factures_paiements_devis_receptions_livraisons()
    // {

    // }


}
