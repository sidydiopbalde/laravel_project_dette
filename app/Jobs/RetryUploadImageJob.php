<?php
namespace App\Jobs;

use App\Facades\UploadCloudImageFacade;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RetryUploadImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }
    
    public function handle()
    {
        // Vérifier si le chemin local de la photo existe
        $uploadedFileUrl = UploadCloudImageFacade::uploadImage($this->user->photo, 'image');
        // dd($uploadedFileUrl);
        $this->user->update(['photo' => $uploadedFileUrl]);
        $this->user->save();
        // if (Storage::exists($this->user->photo)) {
        //     try {
        //         // Tenter de télécharger l'image vers le cloud
        //         // $uploadedFileUrl = UploadCloudImageFacade::uploadImage(storage_path('app/public/' . $this->user->photo), 'image');
                
        //         // Mise à jour de l'URL de la photo dans la base de données
    
        //     } catch (\Exception $e) {
        //         Log::error("Erreur lors de l'upload de l'image vers le cloud pour l'utilisateur {$this->user->id}: " . $e->getMessage());
        //     }
        // } else {
        //     Log::error("Le fichier local n'existe pas : {$this->user->photo}");
        // }
    }
}    

// namespace App\Jobs;

// use App\Facades\UploadCloudImageFacade;
// use App\Models\User;
// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Bus\Dispatchable;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Queue\SerializesModels;
// use Illuminate\Support\Facades\Storage;

// class RetryUploadImageJob implements ShouldQueue
// {
//     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//     protected $user;
//     protected $localPhotoPath;

//     public function __construct(User $user, $localPhotoPath)
//     {
//         $this->user = $user;
//         $this->localPhotoPath = $localPhotoPath;
//     }
    
//     public function handle()
//     {
//         try {
//             $uploadedFileUrl = UploadCloudImageFacade::uploadImage(Storage::path($this->localPhotoPath), 'image');
            
//             // Mettre à jour l'utilisateur avec l'URL de l'image
//             $this->user->update(['photo' => $uploadedFileUrl]);

//             // Supprimer le fichier local après le succès de l'upload
//             Storage::delete($this->localPhotoPath);

//         } catch (\Exception $e) {
//             // En cas d'échec, conserver le fichier local et planifier une nouvelle tentative
//             RetryUploadImageJob::dispatch($this->user, $this->localPhotoPath)
//                 ->delay(now()->addMinutes(1));
//         }
//     }
// } -->
