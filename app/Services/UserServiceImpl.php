<?php

namespace App\Services;

use App\Events\UserCreated;
use App\Events\UsersCreated;
use App\Repository\UserRepository;
use Exception;
use App\Exceptions\ServiceException;
use App\Facades\UploadCloudImageFacade;
use App\Models\Client;
use App\Models\User;
use Illuminate\Support\Facades\DB;
  use Illuminate\Http\UploadedFile;
  use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
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
        if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            // Appeler le service d'upload pour sauvegarder la photo
            // dd($data);
            // $uploadedFileUrl = app(ImageUploadService::class)->uploadImage($data['photo'], 'image');
            $folder = 'images';
            // $filePath=Storage::path($file);
           $filePath = $data['photo']->getRealPath();
           // dd("sidy",$file,Storage::exists($file));
           $uploadedFileUrl = Cloudinary::upload($filePath, [
               'folder' => $folder
            ]);
            $uploadedFileUrl = $uploadedFileUrl->getSecurePath();
            // dd($uploadedFileUrl);
            // Vérifier si l'upload a réussi
            if ($uploadedFileUrl) {
                // Mettre à jour l'URL de la photo dans le tableau de données
                $data['photo'] = $uploadedFileUrl;
            } else {
                throw new \Exception("Erreur lors de l'upload de la photo.");
            }
        }
        //  $user=$this->userRepository->createUser($data);
        // if (isset($data['photo']) && $data['photo'] instanceof UploadedFile) {
            //     $filePath = $data['photo']->store('temp');
            //     event(new UsersCreated($user,  $filePath));
            // }
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
            // if (isset($data['photo'])) {
            //     $filePath = $data['photo']; // Le chemin du fichier
            //     // Transformer en instance UploadedFile
    
            //     $uploadedFile = new UploadedFile($filePath, basename($filePath), null, null, true);
    
            //     // Utiliser le service d'upload pour stocker la photo sur Cloudinary
            //     $photoUrl = app(UploadService::class)->upload($uploadedFile);

             
            // }
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
                'photo' => $data['photo'] ?? null,
            ];
            if (isset($userData['photo']) && $userData['photo'] instanceof UploadedFile) {
                // Appeler le service d'upload pour sauvegarder la photo
                // dd($data);
                // $uploadedFileUrl = app(ImageUploadService::class)->uploadImage($data['photo'], 'image');
                $folder = 'images';
                // $filePath=Storage::path($file);
               $filePath = $userData['photo']->getRealPath();
               // dd("sidy",$file,Storage::exists($file));
               $uploadedFileUrl = Cloudinary::upload($filePath, [
                   'folder' => $folder
                ]);
                $uploadedFileUrl = $uploadedFileUrl->getSecurePath();
                // dd($uploadedFileUrl);
                // Vérifier si l'upload a réussi
                if ($uploadedFileUrl) {
                    // Mettre à jour l'URL de la photo dans le tableau de données
                    $userData['photo'] = $uploadedFileUrl;
                } else {
                    throw new \Exception("Erreur lors de l'upload de la photo.");
                }
            }
            // dd($userData);
            $user = User::create($userData);
            // $uploadedFileUrl = UploadCloudImageFacade::uploadImage($data['photo'], 'image');
            // $user->update(['photo' => $uploadedFileUrl]);
            // $user->save();

            // event(new UserCreated($user,$data['photo']));
            // $user->update['photo',]
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
