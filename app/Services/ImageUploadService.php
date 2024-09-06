<?php

namespace App\Services;


use Illuminate\Support\Facades\Storage;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use GuzzleHttp\Psr7\UploadedFile;

class ImageUploadService
{
    protected $cloudinary;


    /**
     * Upload photo to Cloudinary or fallback to local Base64 storage.
     *
     * @param \Illuminate\Http\UploadedFile $photo
     * @return string|null
     */
    public function uploadImage($file, $type, $customPath = null)
    {
        $folder = $customPath ?? ($type === 'image' ? 'images' : 'documents');
        $file=Storage::path($file);
        
        // dd("sidy",$file,Storage::exists($file));
        $uploadedFile = Cloudinary::upload($file, [
            'folder' => $folder
        ]);
        // Log::info('Uploading file', ['file' => $uploadedFile]);
        return $uploadedFile->getSecurePath();
    }
    // public function uploadImage($file, $type, $customPath = null)
    // {
    //     if (is_string($file) && file_exists($file)) {
    //         // Gérer le cas où $file est un chemin vers un fichier local
    //         $folder = $customPath ?? ($type === 'image' ? 'images' : 'documents');
    
    //         $uploadedFile = Cloudinary::upload($file, [
    //             'folder' => $folder
    //         ]);
    
    //         return $uploadedFile->getSecurePath();
    //     } elseif ($file instanceof UploadedFile) {
    //         // Si $file est un fichier téléversé
    //         $folder = $customPath ?? ($type === 'image' ? 'images' : 'documents');
    
    //         $uploadedFile = Cloudinary::upload($file->getRealPath(), [
    //             'folder' => $folder
    //         ]);
    
    //         return $uploadedFile->getSecurePath();
    //     } else {
    //         throw new \Exception("Le fichier n'est pas valide ou n'est pas un fichier téléversé.");
    //     }
    // }
    
    /**
     * Encode the photo to Base64 and store locally.
     *
     * @param \Illuminate\Http\UploadedFile $photo
     * @return string
     */
    // private function storePhotoAsBase64($photo)
    // {
    //     $photoData = file_get_contents($photo->getRealPath());
    //     $base64Photo = base64_encode($photoData);
    //     $base64Url = 'data:image/' . $photo->getClientOriginalExtension() . ';base64,' . $base64Photo;

    //     // Save Base64 photo to storage (optional if you want to persist it locally)
    //     $photoPath = 'photos/' . uniqid() . '.txt';
    //     Storage::disk('local')->put($photoPath, $base64Url);

    //     return $base64Url; // Return Base64-encoded string
    // }
}