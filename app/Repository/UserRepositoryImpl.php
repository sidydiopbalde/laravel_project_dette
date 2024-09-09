<?php

namespace App\Repository;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRepositoryImpl implements UserRepository 
{
    // Récupérer tous les utilisateurs avec des filtres optionnels
    public function getAllUsers($filters = [])
    {
        $query = User::query();

        // Filtrage par rôle
        if (isset($filters['role'])) {
            $query->where('role_id', $filters['role']);
        }

        // Filtrage par statut actif
        if (isset($filters['active'])) {
            $query->where('active', $filters['active'] === 'oui');
        }

        return $query->get();
    }

    // Récupérer un utilisateur par ID
    public function getUserById($id)
    {
        return User::find($id);
    }

    // Créer un nouvel utilisateur
    public function createUser(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        return User::create($data);
    }

    // Mettre à jour un utilisateur
    public function updateUser($id, array $data)
    {
        $user = User::find($id);
        if ($user) {
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            }
            $user->update($data);
        }
        return $user;
    }

    // Supprimer un utilisateur
    public function deleteUser($id)
    {
        $user = User::find($id);
        if ($user) {
            $user->delete();
        }
        return $user;
    }
}
