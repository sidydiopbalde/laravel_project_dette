<?php

namespace App\Services;

use App\Events\UserCreated;
use App\Repository\ClientRepository;
use App\Http\Requests\StoreRequest;
use App\Facades\ClientRepositoryFacade;
use App\Models\Client;
use App\Services\UploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClientCreated;
use App\Exceptions\ServiceException;
use App\Facades\UploadCloudImageFacade;
use App\Models\User;
use Exception;
use Cloudinary\Cloudinary;
use App\Services\ImageUploadService;

class ClientServiceImpl implements ClientService
{
    public function getClientByPhone(string $phone)
    {
        return ClientRepositoryFacade::findByTelephone($phone);
    }

    public function getAllClients(array $filters)
    {
        return ClientRepositoryFacade::getAll($filters);
    }

    // public function getClientById(int $id, bool $includeUser = false)
    // {
    //     $client = ClientRepositoryFacade::findById($id);

    //     if (!$client) {
    //         // Lever une ServiceException avec des détails
    //         throw new ServiceException('Client not found', 404, null, ['client_id' => $id]);
    //     }

    //     if ($includeUser && $client->user && $client->user->photo) {
    //         $client->user->photo = ClientRepositoryFacade::getBase64($client->user->photo);
    //     }

    //     return $client;
    // }

    public function getClientById(int $id)
    {
        // Récupérer le client par son ID
        $client = ClientRepositoryFacade::findById($id);
    
        // Vérifier si le client existe
        if (!$client) {
            // Lever une ServiceException si le client n'est pas trouvé
            throw new ServiceException('Client not found', 404, null, ['client_id' => $id]);
        }
    
        // Inclure les informations de l'utilisateur associé
        if ($client->user) {
            // Convertir la photo de l'utilisateur en base64 si elle existe
            if ($client->user->photo) {
                $client->user->photo = (new UploadServiceImpl())->encodePhotoToBase64($client->user->photo);
            }
        }
    
        return $client;
    }
    
    public function findByTelephone(string $telephone)
    {
        $client = ClientRepositoryFacade::findByTelephone($telephone);
    
        if (!$client) {
            throw new ServiceException('Client not found');
        }
        if ($client->user && $client->user->photo) {
            $client->user->photo = (new UploadServiceImpl())->encodePhotoToBase64($client->user->photo);
        }
    
        return $client;
    }

    public function createClient(StoreRequest $request)
    {
        try {
            DB::beginTransaction();
            $validatedData = $request->validated();
            $user = null;
    
            if ($request->hasFile('user.photo')) {
                try {
                    $photo = $request->file('user.photo');
                    $photoPath = $photo->store('app/public/photos'); 
                   // event(new UserCreated($user, $photoPath));
                    //  $user->save();
                } catch (\App\Exceptions\ServiceException $e) {
                    throw new ServiceException('Erreur lors de l\'upload de la photo', 500, $e);
                }
            }
            if ($request->has('user')) {
                $userData = collect($validatedData['user'])->toArray();
                $userData['password'] = bcrypt($userData['password']);
                $userData['photo']=  $photoPath ;
                //  dd($userData);
                $user = ClientRepositoryFacade::createUser($userData);
    
                if (!$user) {
                    throw new ServiceException('Erreur lors de la création de l\'utilisateur.', 500);
                }
            }
            $clientData = $validatedData;
            $clientData['user_id'] = $user ? $user->id : null;
            $client = ClientRepositoryFacade::create($clientData);
            if (!$client) {
                throw new ServiceException('Erreur lors de la création du client.', 500);
            }
    
            if ($user) {
                $client->user()->associate($user);
            }
    
            $client->save();
    
            DB::commit();
            return response()->json(['client' => $client], 201);
        } catch (ServiceException $e) {
        
            DB::rollBack();
            throw new ServiceException('Erreur lors de la création du client.', 500, $e);
        }
    }
    
    
    
}