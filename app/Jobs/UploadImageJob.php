<?php

namespace App\Jobs;

use App\Facades\UploadCloudImageFacade;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UploadImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $photo;
    protected $userId;

    public function __construct($photo, $userId)
    {
        $this->photo = $photo;
        $this->userId = $userId;
    }

    public function handle()
    {
        // Uploader l'image sur Cloudinary
        $uploadedFileUrl = UploadCloudImageFacade::uploadImage($this->photo, 'image');

        // Mise à jour de l'utilisateur avec l'URL de la photo
        $user = User::find($this->userId);
        $user->photo = $uploadedFileUrl;
        $user->save();
    }
}

