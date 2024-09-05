<?php

namespace App\Services;

use App\Repository\ClientRepository;
use App\Http\Requests\StoreRequest;
use App\Facades\ClientRepositoryFacade;
use App\Models\Client;
use App\Services\UploadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClientCreated;
use App\Exceptions\ServiceException;
use Exception;
use Cloudinary\Cloudinary;
use Cloudinary\Uploader;

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
            DB::beginTransaction();
            
            $validatedData = $request->validated();

            // Création de l'utilisateur, si fourni
            $user = null;
            if ($request->has('user')) {
                $userData = $validatedData['user'];
                $userData['password'] = bcrypt($userData['password']);
                $user = ClientRepositoryFacade::createUser($userData);
            }
            
            // Préparer les données client
            $clientData = $validatedData;
            $clientData['user_id'] = $user ? $user->id : null;

            if ($request->hasFile('photo')) {
                $filePath = $request->file('photo')->store('photos', 'public');
                $clientData['photo'] = $filePath;
            }

            // Création du client
            $client = ClientRepositoryFacade::create($clientData);

            if ($user) {
                $client->user()->associate($user);
            }

            $client->save();

            // Génération du QR code avec les informations du client
            $qrCodeData = json_encode([
                'id' => $client->id,
                'name' => $client->surnom,
                'mail' => $client->user->mail,
                'phone' => $client->user->phone,
            ]);
            $qrCodeFileName = 'client_' . $client->id . '.png';
            $qrCodePath = app(QrCodeService::class)->generateQrCode($qrCodeData, $qrCodeFileName);
            
            // Génération du PDF avec le client et le QR code
            $pdfPath = storage_path('public/pdfs/client_' . $client->id . '.pdf');
            app(pdfService::class)->generatePdf('pdf.client', ['client' => $client, 'qrCodePath' => $qrCodePath], $pdfPath);
            
            // Envoyer l'e-mail avec le PDF en pièce jointe
            Mail::to($client->user->mail)->send(new ClientCreated($client, $pdfPath));

            DB::commit();

            return response()->json(['client' => $client, 'pdf' => $pdfPath]);

        } catch (Exception $e) {
            DB::rollBack();
            throw new ServiceException('Erreur lors de la création du client: ' . $e->getMessage(), 0, $e);
        }
    }
}
