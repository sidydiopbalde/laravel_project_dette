<?php
namespace App\Http\Controllers;

use App\Services\ArchiveServiceCommunInterface;
use App\Services\ArchiveServiceFactory;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class ArchiveController extends Controller
{
    protected $archiveService;

   
    public function __construct(ArchiveServiceCommunInterface $archiveService)
    {
        $this->archiveService = $archiveService;
    }
    public function showArchivedDettes(Request $request)
    {
        // Récupérer les filtres depuis la requête (client_id et date)
        $filter = $request->only(['client_id', 'date']);
        
        // Déterminer le service à utiliser (Firebase ou MongoDB) via la variable d'environnement
        $serviceType = env('ARCHIVE_SERVICE', 'mongoDB'); // Par défaut 'mongo'
        $service = ($serviceType === 'firebase') ? App::make(FirebaseService::class) : $this->archiveService;

        try {
            // Récupérer les dettes archivées en fonction des filtres
            $archives = $service->getArchivedDettes($filter);
            
            return response()->json([
                'success' => true,
                'message' => 'Dettes archivées récupérées avec succès',
                'data' => $archives,
            ], 200);
        } catch (\Exception $e) {
            // Gérer les exceptions et retourner un message d'erreur
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des dettes archivées',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function getArchivedDettesByClient($clientId)
    {
        try {
            // Utiliser la factory pour créer le service d'archivage (Mongo ou Firebase)
            $archiveService = ArchiveServiceFactory::create();
            $dettes = $archiveService->getArchivedDettesByClient($clientId);
            // dd($dettes);

            return response()->json([
                'success' => true,
                'message' => 'Dettes archivées récupérées avec succès',
                'data' => $dettes,
            ], 200);
        } catch (\Exception $e) {
            // Gérer les exceptions et retourner un message d'erreur
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des dettes archivées',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function getArchivedDetteDetailsById($detteId)
    {
       
        try {
            $serviceType = env('ARCHIVE_SERVICE', 'mongoDB'); // Par défaut 'mongoDB'
            $service = ($serviceType === 'firebase') ? App::make(FirebaseService::class) : $this->archiveService;

            $detteDetails = $service->getArchivedDetteDetailsById($detteId);
           
            return response()->json([
                'success' => true,
                'message' => 'Détails de la dette récupérés avec succès',
                'data' => $detteDetails,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des détails de la dette',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function restoreArchivedDetteById($detteId)
    {
        // dd($detteId);
        try {
            $serviceType = env('ARCHIVE_SERVICE', 'mongoDB'); // Par défaut 'mongoDB'
            $service = ($serviceType === 'firebase') ? App::make(FirebaseService::class) : $this->archiveService;

            $archiveDette = $service->restoreArchivedDetteById($detteId);
         
            return response()->json([
                'success' => true,
                'message' => 'Détails de la dette récupérés avec succès',
                'data' => $archiveDette,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des détails de la dette',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
    public function restoreArchivedDettesByDate($date)
    {
       
        try {
            $serviceType = env('ARCHIVE_SERVICE', 'mongoDB'); // Par défaut 'mongoDB'
            $service = ($serviceType === 'firebase') ? App::make(FirebaseService::class) : $this->archiveService;

            $archiveDette = $service->restoreArchivedDettesByDate($date);
          
            return response()->json([
                'success' => true,
                'message' => 'Détails de la dette récupérés avec succès',
                'data' => $archiveDette,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des détails de la dette',
                'details' => $e->getMessage(),
            ], 500);
        }
    }

    public function restoreArchivedDettesByClient($clientId)
    {
        try {
            // Utiliser la factory pour créer le service d'archivage (Mongo ou Firebase)
            $archiveService = ArchiveServiceFactory::create();
            $dettes = $archiveService->restoreArchivedDettesByClient($clientId);
            // dd($dettes);

            return response()->json([
                'success' => true,
                'message' => 'Dettes archivées récupérées avec succès pour le client ',
                'data' => $dettes,
            ], 200);
        } catch (\Exception $e) {
            // Gérer les exceptions et retourner un message d'erreur
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des dettes archivées',
                'details' => $e->getMessage(),
            ], 500);
        }
    }
}

