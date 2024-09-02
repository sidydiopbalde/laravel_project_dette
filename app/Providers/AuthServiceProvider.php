<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Article;
use App\Models\User;
use App\Policies\ArticlePolicy;
use App\Policies\UserPolicy;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Les policies pour les modèles.
     *
     * @var array
     */
    protected $policies = [
        Article::class => ArticlePolicy::class,
        User::class => UserPolicy::class,
        // Ajoutez d'autres policies ici si nécessaire
    ];

    /**
     * Enregistrez les services d'autorisation.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
  // Définir les scopes disponibles
    Passport::tokensCan([
        'view-user-data' => 'Voir les données utilisateur',
        'admin' => 'Accès administrateur',
    ]);
        // Vous pouvez également définir des gates ici si nécessaire
        Gate::define('access-articles', [ArticlePolicy::class, 'access']);
    }
}
