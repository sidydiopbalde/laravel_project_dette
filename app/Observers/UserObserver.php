<?php

namespace App\Observers;

use App\Events\UserCreated;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the client "created" event.
     */
    public function created(User $user)
    {
        // dd($user->photo);
        // Gérer l'upload de la photo après la création du client, si la photo est présente
        // $user = $client->user;

        if ($user && $user->photo) {
            try {
             
                $photoPath = $user->photo; 
               
                event(new UserCreated($user, $photoPath));
            } catch (\Exception $e) {
                Log::error("Erreur lors de l'upload de la photo pour l'utilisateur {$user->id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Handle the client "updated" event.
     */
    public function updated(User $client): void
    {
        //
    }

    /**
     * Handle the client "deleted" event.
     */
    public function deleted(User $client): void
    {
        //
    }

    /**
     * Handle the client "restored" event.
     */
    public function restored(User $client): void
    {
        //
    }

    /**
     * Handle the client "force deleted" event.
     */
    public function forceDeleted(User $client): void
    {
        //
    }
}
