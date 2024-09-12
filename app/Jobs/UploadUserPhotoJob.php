<?php

namespace App\Jobs;

use App\Models\User;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class UploadUserPhotoJob implements ShouldQueue
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
        // Uploader l'image vers Cloudinary
        // $uploadedFile = Cloudinary::upload($this->photo->getRealPath(), [
        //     'folder' => 'images'
        // ]);

        // // Extraire l'URL de l'image uploadée
        // $uploadedFileUrl = $uploadedFile->getSecurePath();

        // // Mettre à jour l'URL de la photo dans la base de données
        // $this->user->update(['photo' => $uploadedFileUrl]);
         // Obtenir le chemin réel du fichier
         $filePath = Storage::path($this->photo);

         // Uploader l'image vers Cloudinary
         $uploadedFile = Cloudinary::upload($filePath, [
             'folder' => 'images'
         ]);
 
         // Extraire l'URL de l'image uploadée
         $uploadedFileUrl = $uploadedFile->getSecurePath();
 
         // Mettre à jour l'URL de la photo dans la base de données
         $this->user->update(['photo' => $uploadedFileUrl]);
 
         // Supprimer le fichier temporaire après l'upload
        //  Storage::delete($this->filePath);
    }
}
