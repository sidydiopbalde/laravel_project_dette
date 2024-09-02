<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class ArticlePolicy
{
    /**
     * Détermine si l'utilisateur peut accéder aux articles.
     */
    public function access(User $user): Response
    {
        // Autorise l'accès si le rôle de l'utilisateur est 'Boutiquier'
        return $user->role && $user->role->libelle === 'Boutiquier'
            ? Response::allow()
            : Response::deny('Vous devez être un Boutiquier pour accéder à cette ressource.');
    }

    // Ajoutez des méthodes pour d'autres actions comme view, create, update, delete, etc.
}


