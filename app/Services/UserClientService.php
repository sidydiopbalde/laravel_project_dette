<?php
namespace App\Services;

use App\Models\User;
use App\Models\Clients;
use Illuminate\Support\Facades\DB;
use Exception;

class UserClientService
{
    public function storeUserClientExist(array $data)
    {
        try {
            // Début de la transaction
            DB::beginTransaction();

            // Création de l'utilisateur
            $userData = [
                'prenom' => $data['prenom'],
                'nom' => $data['nom'],
                'password' => bcrypt($data['password']),
                'login' => $data['login'],
                'mail' => $data['mail'],
                'role_id' => 3,
                'photo' => $data['photo_path'] ?? null,
            ];

            $user = User::create($userData);

            // Association du client à l'utilisateur
            $client = Clients::find($data['client_id']);

            if (!$client) {
                throw new Exception('Client non trouvé.');
            }

            $client->user()->associate($user);
            $client->save();

            // Validation de la transaction
            DB::commit();

            return $user;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Erreur lors de la création de l'utilisateur: " . $e->getMessage());
        }
    }
}
