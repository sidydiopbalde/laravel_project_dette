<?php

namespace App\Services;

use App\Repository\UserRepository;
use Exception;
use App\Exceptions\ServiceException;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;
  use Illuminate\Http\UploadedFile;
class UserServiceImpl implements UserService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
  

  
    
    public function getAllUsers($filters = [])
    {
        return $this->userRepository->getAllUsers($filters);
    }

    public function getUserById($id)
    {
        $user = $this->userRepository->getUserById($id);
        if (!$user) {
            throw new ServiceException('User not found', 404);
        }
        return $user;
    }

    public function createUser(array $data)
    {
        // Vérifier si le login existe déjà
    //   if($data['role_id'])
       
        return $this->userRepository->createUser($data);
    }

    public function updateUser($id, array $data)
    {
        $user = $this->userRepository->updateUser($id, $data);
        if (!$user) {
            throw new ServiceException('User not found', 404);
        }
        return $user;
    }

    public function deleteUser($id)
    {
        $user = $this->userRepository->deleteUser($id);
        if (!$user) {
            throw new ServiceException('User not found', 404);
        }
        return $user;
    }
    public function storeUserClientExist(array $data)
    {
        try {
            // Début de la transaction
            DB::beginTransaction();

            $photoUrl = null;

            // dd($data['photo']);
            // Si la photo est une chaîne représentant un chemin de fichier
            if (isset($data['photo']) && is_string($data['photo'])) {
                $filePath = $data['photo']; // Le chemin du fichier
                // Transformer en instance UploadedFile
    
                $uploadedFile = new UploadedFile($filePath, basename($filePath), null, null, true);
    
                // Utiliser le service d'upload pour stocker la photo sur Cloudinary
                $photoUrl = app(UploadService::class)->upload($uploadedFile);
            }
            // $cloudinaryService = app(UploadService::class);
            // $photoUrl = null;
            // if (isset($data['photo'])) {
            //     $photoUrl = $cloudinaryService->upload($data['photo'],'images');
            // }
    
            // Création de l'utilisateur
            $userData = [
                'prenom' => $data['prenom'],
                'nom' => $data['nom'],
                'password' => bcrypt($data['password']),
                'login' => $data['login'],
                'mail' => $data['mail'],
                'role_id' => 3,
                'photo' => $photoUrl ?? null,
            ];

            $user = User::create($userData);

            // Association du client à l'utilisateur
            $client = Client::find($data['client_id']);

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
