<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory , HasApiTokens;
    protected $fillable = ['prenom', 'nom', 'login', 'mail', 'password', 'role_id','photo'];
    protected $hidden = ['password', 'remember_token','created_at', 'updated_at'];

    // Définir les attributs qui ne peuvent pas être assignés en masse
    // protected $guarded = [ 'created_at', 'updated_at'];
    
    public function clients()
    {
        return $this->hasOne(Clients::class)->nullable();
    }
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
    
    // public function users_roles()

}
