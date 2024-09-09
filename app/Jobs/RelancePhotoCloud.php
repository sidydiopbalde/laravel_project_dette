<?php



namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Facades\UploadCloudImageFacade;

class RelancePhotoCloud implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct() {}

    public function handle()
    {
        // Récupérer les utilisateurs dont les photos sont stockées localement
        $users = User::where(function ($query) {
            $query->whereNull('photo')
                  ->orWhere('photo', 'like', '%/public/temp/%'); // Local storage path
        })->get();

        if ($users->isEmpty()) {
            Log::info('Aucun utilisateur avec des photos en attente de téléchargement.');
            return;
        }

        // Parcourir chaque utilisateur et relancer l'upload de leur photo
        foreach ($users as $user) {
            try {
                Log::info("Relancer l'upload pour l'utilisateur: {$user->id}");

                // Chemin de la photo dans le stockage local
                $photoPath = 'public/temp/' . basename($user->photo);

                if (Storage::exists($photoPath)) {
                    // Relancer le job pour uploader la photo
                    UploadImageJob::dispatch($user, $photoPath);
                } else {
                    Log::error("Le fichier photo n'existe pas pour l'utilisateur {$user->id}: {$user->photo}");
                }
            } catch (\Exception $e) {
                Log::error("Erreur lors de la relance de l'upload pour l'utilisateur {$user->id}: {$e->getMessage()}");
            }
        }
    }
}

// class RelancerUploadImages extends Command
// {
//     protected $signature = 'images:relancer';
//     protected $description = 'Relance l\'upload des images non sauvegardées dans le cloud';

//     public function __construct()
//     {
//         parent::__construct();
//     }

//     public function handle()
//     {
//         // Récupérer les utilisateurs avec des photos en statut 'pending' ou 'error'
//         $users = User::whereIn('photo_status', ['pending', 'error'])
//             ->where(function ($query) {
//                 $query->whereNull('photo')
//                       ->orWhere('photo', 'like', '%/public/temp/%'); // Local storage path
//             })
//             ->get();

//         if ($users->isEmpty()) {
//             Log::info('Aucun utilisateur avec des photos en attente de téléchargement.');
//             return;
//         }

//         // Parcourir chaque utilisateur pour relancer l'upload
//         foreach ($users as $user) {
//             try {
//                 Log::info("Relancer l'upload pour l'utilisateur: {$user->id}");

//                 // Chemin de la photo dans le stockage local
//                 $photoPath = 'public/temp/' . basename($user->photo);

//                 if (Storage::exists($photoPath)) {
//                     // Relancer le job d'upload d'image
//                     UploadImageJob::dispatch($user, $photoPath);

//                     // Mettre à jour le statut de la photo à "processing"
//                     $user->update(['photo_status' => 'processing']);
//                 } else {
//                     Log::error("Le fichier photo n'existe pas pour l'utilisateur {$user->id}: {$user->photo}");
//                     $user->update(['photo_status' => 'error']);
//                 }
//             } catch (\Exception $e) {
//                 Log::error("Erreur lors de la relance de l'upload pour l'utilisateur {$user->id}: {$e->getMessage()}");
//                 $user->update(['photo_status' => 'error']);
//             }
//         }
//     }
// }

// namespace App\Jobs;

// use App\Models\User;
// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Bus\Dispatchable;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Queue\SerializesModels;
// use Illuminate\Support\Facades\Log;
// use Illuminate\Support\Facades\Storage;

// class RelancerImageCloudJob implements ShouldQueue
// {
//     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//     public function __construct()
//     {
//         //
//     }

//     public function handle()
//     {
//         // Récupérer les utilisateurs avec des photos en statut 'pending' ou 'error'
//         $users = User::whereIn('photo_status', ['pending', 'error'])
//             ->where(function ($query) {
//                 $query->whereNull('photo')
//                       ->orWhere('photo', 'like', '%/public/temp/%'); // Local storage path
//             })
//             ->get();

//         if ($users->isEmpty()) {
//             Log::info('Aucun utilisateur avec des photos en attente de téléchargement.');
//             return;
//         }

//         // Parcourir chaque utilisateur pour relancer l'upload
//         foreach ($users as $user) {
//             try {
//                 Log::info("Relancer l'upload pour l'utilisateur: {$user->id}");

//                 // Chemin de la photo dans le stockage local
//                 $photoPath = 'public/temp/' . basename($user->photo);

//                 if (Storage::exists($photoPath)) {
//                     // Relancer le job d'upload d'image
//                     UploadImageJob::dispatch($user, $photoPath);

//                     // Mettre à jour le statut de la photo à "processing"
//                     $user->update(['photo_status' => 'processing']);
//                 } else {
//                     Log::error("Le fichier photo n'existe pas pour l'utilisateur {$user->id}: {$user->photo}");
//                     $user->update(['photo_status' => 'error']);
//                 }
//             } catch (\Exception $e) {
//                 Log::error("Erreur lors de la relance de l'upload pour l'utilisateur {$user->id}: {$e->getMessage()}");
//                 $user->update(['photo_status' => 'error']);
//             }
//         }
//     }
// }
