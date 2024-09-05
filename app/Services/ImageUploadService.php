<?php

namespace App\Services;


use Illuminate\Support\Facades\Storage;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
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

        $uploadedFile = Cloudinary::upload($file->getRealPath(), [
            'folder' => $folder
        ]);
        // Log::info('Uploading file', ['file' => $uploadedFile]);
        return $uploadedFile->getSecurePath();
    }

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