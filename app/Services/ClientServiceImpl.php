<?php

namespace App\Services;

use App\Events\UserCreated;
use App\Repository\ClientRepository;
use App\Http\Requests\StoreRequest;
use App\Facades\ClientRepositoryFacade;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use App\Exceptions\ServiceException;
use App\Models\Notification;
use App\Models\Paiement;
use App\Notifications\DebtReminderNotification;
use Illuminate\Support\Facades\Notification as NotificationFacade;

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
    
    public function notifyClientsWithDebts()
    {
        $clients = Client::with('dettes')->get();

        foreach ($clients as $client) {
            $montantTotalRestant = 0;

            foreach ($client->dettes as $dette) {
                $montantPaye = Paiement::where('dette_id', $dette->id)->sum('montant');
                $montantRestant = $dette->montant - $montantPaye;

                if ($montantRestant > 0) {
                    $montantTotalRestant += $montantRestant;
                }
            }

            if ($montantTotalRestant > 0) {
                $message = "Bonjour {$client->surnom}, il vous reste un total de {$montantTotalRestant} à payer pour vos dettes.";
                $client->notify(new DebtReminderNotification($message));
            }
        }
    }
    public function sendDebtReminder($clientId)
    {
        // Trouver le client
        $client = Client::findOrFail($clientId);
        // dd($client);
        $montantTotalRestant = 0;
        foreach ($client->dettes as $dette) {
            $montantPaye = $dette->paiements()->sum('montant');
            $montantRestant = $dette->montant - $montantPaye;

            if ($montantRestant > 0) {
                $montantTotalRestant += $montantRestant;
            }
        }

        if ($montantTotalRestant > 0) {
            $message = "Bonjour {$client->surnom}, il vous reste un total de {$montantTotalRestant} à payer pour vos dettes.";

            // Envoyer la notification
            NotificationFacade::send($client, new DebtReminderNotification($message));

            // Stocker la notification en base de données
            Notification::create([
                'client_id' => $client->id,
                'message' => $message,
            ]);

            return ['success' => true, 'message' => 'Notification envoyée avec succès.'];
        }

        return ['success' => false, 'message' => 'Aucun montant dû.'];
    }
    
}