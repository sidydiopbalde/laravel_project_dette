<?php

namespace App\Jobs;

use App\Mail\ClientCreated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendClientEmailJob implements ShouldQueue
{
    use Queueable, InteractsWithQueue, SerializesModels,Dispatchable;

    protected $user;
    protected $pdfPath;

    public function __construct(User $user, $pdfPath)
    {
        $this->user = $user;
        $this->pdfPath = $pdfPath;
    }

    public function handle()
    {
        try {
          
           // Mauvais : vous passez uniquement l'adresse e-mail
        // Mail::to($this->user->mail)->send(new ClientCreated($this->user->mail, $this->pdfPath));


        Mail::to($this->user->mail)->send(new ClientCreated($this->user, $this->pdfPath));


        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de l\'e-mail : ' . $e->getMessage());
        }
    }
}
