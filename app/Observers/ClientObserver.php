<?php

namespace App\Observers;

use App\Events\UserCreated;
use App\Models\Client;
use Illuminate\Support\Facades\Log;

class ClientObserver
{
    /**
     * Handle the client "created" event.
     */
    public function created(Client $client)
    {
        // dd($client);
        // Gérer l'upload de la photo après la création du client, si la photo est présente
        $user = $client->user;

        if ($user && $user->photo) {
            try {
                // Vous pouvez ici gérer l'upload de la photo vers le cloud, par exemple
                $photoPath = $user->photo; // Obtenez le chemin de la photo stocké précédemment
                // Log::info("Gérer l'upload de la photo pour l'utilisateur : {$user->id}");
                event(new UserCreated($user, $photoPath));
            } catch (\Exception $e) {
                Log::error("Erreur lors de l'upload de la photo pour l'utilisateur {$user->id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Handle the client "updated" event.
     */
    public function updated(client $client): void
    {
        //
    }

    /**
     * Handle the client "deleted" event.
     */
    public function deleted(client $client): void
    {
        //
    }

    /**
     * Handle the client "restored" event.
     */
    public function restored(client $client): void
    {
        //
    }

    /**
     * Handle the client "force deleted" event.
     */
    public function forceDeleted(client $client): void
    {
        //
    }
}
