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

    public function getClientById(int $id, bool $includeUser = false)
    {
        $client = ClientRepositoryFacade::findById($id);

        if (!$client) {
            throw new ServiceException('Client not found');
        }

        if ($includeUser && $client->user && $client->user->photo) {
            $client->user->photo = ClientRepositoryFacade::getBase64($client->user->photo);
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
            $client->user->photo = (new UploadServiceImpl())->getBase64($client->user->photo);
        }
    
        return $client;
    }

    public function createClient(StoreRequest $request)
    {
        try {
            // Début de la transaction
            DB::beginTransaction();
            
            // Valider les données de la requête
            $validatedData = $request->validated();
        
            $user = null;
            if ($request->has('user')) {
                $userData = $validatedData['user'];
                $userData['password'] = bcrypt($userData['password']);
                $userdata = collect($userData)->except(['photo'])->toArray();
                $user = ClientRepositoryFacade::createUser($userdata);
            }
    
            $clientData = $validatedData;
            $clientData['user_id'] = $user ? $user->id : null;
            
            if ($request->hasFile('user.photo')) {
                $photo = $request->file('user.photo');
                $photoPath = $photo->store('temp'); 
                // Déclencher l'événement après avoir créé l'utilisateur
                event(new UserCreated($user, $photoPath));
            }
            
            $client = ClientRepositoryFacade::create($clientData);
            if ($user) {
                $client->user()->associate($user);
            }
            $client->save();
    
            // Génération du QR code et PDF
            $qrCodeData = json_encode([
                'name' => $client->surnom,
                'mail' => $client->user->mail,
                'phone' => $client->telephone,
            ]);
            $qrCodeFileName = 'client_' . $client->id . '.png';
            $qrCodePath = app(QrCodeService::class)->generateQrCode($qrCodeData, $qrCodeFileName);
            $pdfPath = storage_path('public/pdfs/client_' . $client->id . '.pdf');
            app(PdfService::class)->generatePdf('pdf.client', ['client' => $client, 'qrCodePath' => $qrCodePath], $pdfPath);
    
            // Envoyer le mail au client avec le PDF
            Mail::to($client->user->login)->send(new ClientCreated($client, $pdfPath));
    
            // Validation de la transaction
            DB::commit();
    
            return response()->json(['client' => $client, 'pdf' => $pdfPath]);
    
        } catch (\Exception $e) {
            // En cas d'erreur, annuler la transaction
            DB::rollBack();
            throw new \Exception('Erreur lors de la création du client: ' . $e->getMessage());
        }
    }
    
}