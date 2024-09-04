<?php
namespace App\Services;


use App\Services\ClientService;
use App\Repository\ClientRepository;
use App\Http\Requests\StoreRequest;
use App\Facades\ClientRepositoryFacade;
use App\Models\Client;
use App\Services\UploadService;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Color\Color;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Services\pdfService;
use App\Services\QrCodeService;
use Illuminate\Support\Facades\Mail;
use App\Mail\ClientCreated;

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

   
    // Récupération d'un client par ID, avec ou sans l'utilisateur associé
    public function getClientById(int $id, bool $includeUser = false)
    {
        $client = ClientRepositoryFacade::findById($id, $includeUser);

        if (!$client) {
            throw new \Exception('Client not found');
        }

        if ($includeUser && $client->user && $client->user->photo) {
            $client->user->photo = ClientRepositoryFacade::getBase64($client->user->photo);
        }

        return $client;
    }
    public function findByTelephone(string $telephone)
    {
        // Récupérer le client avec l'utilisateur associé
        $client = ClientRepositoryFacade::findByTelephone($telephone);
    
        if (!$client) {
            throw new \Exception('Client not found');
        }
    
        // Si l'utilisateur a une photo, la convertir en base64
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

            // Gestion du fichier de la photo
            if ($request->hasFile('photo')) {
                $filePath = $request->file('photo')->store('photos', 'public');
                $clientData['photo'] = $filePath;
            }

            // Création du client
            $client = ClientRepositoryFacade::create($clientData);

            // Associer le client à l'utilisateur, si existant
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
            $qrCodePath=app(QrCodeService::class)->generateQrCode($qrCodeData,$qrCodeFileName);
            $photoPath=$client->user->photo;
            // Génération du PDF avec le client et le QR code
            $pdfPath = storage_path('public/pdfs/client_' . $client->id . '.pdf');
            app(PdfService::class)->generatePdf('pdf.client', ['client' => $client,'qrCodePath' => $qrCodePath,], $pdfPath);
            // Envoyer l'e-mail avec le PDF en pièce jointe
            Mail::to($client->user->mail)->send(new ClientCreated($client, $pdfPath));
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
