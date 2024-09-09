<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Jobs\RetryUploadImageJob;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RetryUploadImages extends Command
{
    protected $signature = 'images:retry-upload-images';
    protected $description = 'Retry uploading images that failed to upload to the cloud';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Obtenez la liste des fichiers locaux stockés dans le répertoire temporaire
        $files = Storage::files('public/temp');
        foreach ($files as $file) {

            // Obtenez l'ID de l'utilisateur à partir du nom du fichier
            $userId = (int)pathinfo($file, PATHINFO_FILENAME);
            // Récupérez l'utilisateur correspondant
            $user = User::find($userId);
            
            if ($user) {
                // Enfilez le job pour relancer l'upload
                RetryUploadImageJob::dispatch($user, $file);
            }
            Log::error($file);
        }

        $this->info('Image upload retries dispatched successfully.');
    }
}
