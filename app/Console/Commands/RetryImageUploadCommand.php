<?php
namespace App\Console\Commands;

use App\Jobs\RetryUploadImageJob;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class RetryImageUploadCommand extends Command
{
    protected $signature = 'images:retry-upload';
    protected $description = 'Relancer l\'upload des images non uploadées dans le cloud';

    public function handle()
    {
        // Sélectionner les utilisateurs dont la photo est stockée localement
        $users = User::where('photo', 'like', '%app/public/photos/%')->get();
        // dd($users);
        // dd($users);
        if ($users->isEmpty()) {
            Log::info('Aucune image à relancer.');
            return;
        }

        foreach ($users as $user) {
            // Relancer l'upload pour chaque utilisateur
            RetryUploadImageJob::dispatch($user);
        }

        $this->info('Relance de l\'upload des images terminée.');
    }
}
