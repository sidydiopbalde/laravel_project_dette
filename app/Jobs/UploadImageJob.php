<?php
// namespace App\Jobs;

// use App\Facades\ImageUploadFacade;
// use App\Models\User;
// use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Bus\Dispatchable;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Queue\SerializesModels;
// use Illuminate\Support\Facades\Storage;
// use App\Facades\UploadCloudImageFacade;
// class UploadImageJob implements ShouldQueue
// {
//     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//     protected $user;
//     protected $photo;
//     public function __construct(User $user,$photo)
//     {
//         $this->user = $user;
//         $this->photo = $photo;

//     }
    
//     public function handle()
//     {
//         if ($this->photo) {
//             try {
//                 $uploadedFileUrl = UploadCloudImageFacade::uploadImage($this->photo,'image');
                
//                 $this->user->update(['photo' => $uploadedFileUrl]);
//             } catch (\Exception $e) {
             
//                  $localPath = 'public/temp/' . $this->user->id . '_photo.jpg';
            
//                  Storage::put($localPath, file_get_contents($this->photo));
//                  // Mise à jour de la base de données avec le chemin local
//                  $this->user->update(['photo' => $localPath]);
//             }
//         }
//     }
//     protected function storeLocalPhoto()
//     {
//         $localPath = 'public/temp/' . $this->user->id . '_photo.jpg';
//         Storage::put($localPath, file_get_contents($this->photo));
        
//         // Mettre à jour la base de données avec le chemin local
//         $this->user->update(['photo' => $localPath]);

//         // Planifier une relance du job
//         UploadImageJob::dispatch($this->user, $this->photo)->delay(now()->addMinutes(10));
//     }

//     protected function cleanupLocalPhoto()
//     {
//         $photoPath = $this->user->photo;

//         // Vérifiez si le chemin est un chemin local
//         if (Storage::exists($photoPath) && Storage::url($photoPath) !== $photoPath) {
//             Storage::delete($photoPath);
//         }
//     }
// }




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

class UploadImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $photo;

    public function __construct(User $user, $photo)
    {
        $this->user = $user;
        $this->photo = $photo;
    }
    
    public function handle()
    {
        if ($this->photo) {
            // dd($this->photo);
            try {
                // Tenter de télécharger l'image vers le cloud
                $uploadedFileUrl = UploadCloudImageFacade::uploadImage($this->photo, 'image');
                $this->user->update(['photo' => $uploadedFileUrl]);
              
            } catch (\Exception $e) {
                Log::info($this->photo);
                $fileName=time().'.'.$this->photo->extension();
                $file=$this->photo->storeAs('image',   $fileName,
                [
                    'disk' => 'public'
                ]);
                // $localPath = 'public/temp/' . $this->user->id . '_photo.jpg';

                // $this->user->update(['photo' => $this->photo]);
                // $this->user->save();
                // if (!Storage::disk('public')->exists('temp')) {
                //     Storage::disk('public')->makeDirectory('temp');
                // }
                
                // // Storage::disk('public')->put($localPath, file_get_contents($this->photo));
                // if (Storage::exists($this->photo)) {
                //     // dd(Storage::exists($this->photo));
                //     Storage::disk('public')->put($localPath, file_get_contents(storage_path('app/public/' . $this->photo)));
                // } else {
                //     Log::error("Le fichier photo n'existe pas : {$this->photo}");
                // }             
            }
        }
    }

    // protected function storeLocalPhoto()
    // {
    //     $localPath = 'public/temp/' . $this->user->id . '_photo.jpg';
    //     // Storage::disk('public')->put($localPath, file_get_contents($this->photo));

    //     if (!Storage::disk('public')->exists('temp')) {
    //         Storage::disk('public')->makeDirectory('temp');
    //     }

    //     // Stocker le fichier localement
    //     Storage::disk('public')->put($localPath, file_get_contents($this->photo));
    //     $this->user->update(['photo' => $localPath]);
    //     $this->user->save();
    //     // Planifier une relance du job pour uploader la photo stockée localement
    //     RetryUploadImageJob::dispatch($this->user, $localPath)
    //         ->delay(now()->addMinutes(1));
    // }

    // protected function cleanupLocalPhoto()
    // {
    //     $photoPath = $this->user->photo;

    //     // Vérifiez si le chemin est un chemin local
    //     if (Storage::exists($photoPath) && Storage::url($photoPath) !== $photoPath) {
    //         Storage::delete($photoPath);
    //     }
    // }
}
