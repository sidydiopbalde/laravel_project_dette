<?php
namespace App\Jobs;

use App\Facades\ImageUploadFacade;
use App\Models\User;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;
use App\Facades\UploadCloudImageFacade;
class UploadImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $photo;
    public function __construct(User $user,$photo)
    {
        $this->user = $user;
        $this->photo = $photo;

    }
    
    public function handle()
    {
        if ($this->photo) {
            try {
                $uploadedFileUrl = UploadCloudImageFacade::uploadImage($this->photo,'image');
                // dd($uploadedFileUrl);
                
                $this->user->update(['photo' => $uploadedFileUrl]);

            } catch (\Exception $e) {
                // dd("hghj");
                // En cas d'erreur, la photo peut être stockée localement
                // et un job de relance peut être déclenché pour essayer à nouveau
                Storage::put('public/temp/' . $this->user->id . '_photo.jpg', file_get_contents($this->user->photo));
            }
        }
    }
}