<?php
 namespace App\Services;

 use App\Models\Client;
 use App\Notifications\DebtReminderNotification;
 use Illuminate\Support\Facades\Notification as NotificationFacade;
 use App\Models\Notification as NotificationModel;
 
 class NotificationService
 {
    public function sendDebtReminder($clientId)
    {
        // Trouver le client
        $client = Client::findOrFail($clientId);
        // dd($client);
        $montantTotalRestant = 0;
        foreach ($client->dettes as $dette) {
            $montantPaye = $dette->paiements()->sum('montant');
            $montantRestant = $dette->montant - $montantPaye;

            if ($montantRestant > 0) {
                $montantTotalRestant += $montantRestant;
            }
        }

        if ($montantTotalRestant > 0) {
            $message = "Bonjour {$client->surnom}, il vous reste un total de {$montantTotalRestant} à payer pour vos dettes.";

            // Envoyer la notification
            NotificationFacade::send($client, new DebtReminderNotification($message));

            // Stocker la notification en base de données
            NotificationModel::create([
                'client_id' => $client->id,
                'message' => $message,
            ]);

            return ['success' => true, 'message' => 'Notification envoyée avec succès.'];
        }

        return ['success' => false, 'message' => 'Aucun montant dû.'];
    }
     public function sendNotificationsToAllClients()
     {
         $clients = Client::with('dettes')->get();
 
         foreach ($clients as $client) {
             $montantTotalRestant = $this->calculateRemainingAmount($client);
 
             if ($montantTotalRestant > 0) {
                 $message = "Bonjour {$client->surnom}, il vous reste un total de {$montantTotalRestant} à payer pour vos dettes.";
 
                 // Envoyer la notification
                 NotificationFacade::send($client, new DebtReminderNotification($message));
 
                 // Stocker la notification en base de données
                 NotificationModel::create([
                     'client_id' => $client->id,
                     'message' => $message,
                 ]);
             }
         }
     }
 
     private function calculateRemainingAmount($client)
     {
         $montantTotalRestant = 0;
         foreach ($client->dettes as $dette) {
             $montantPaye = $dette->paiements()->sum('montant');
             $montantRestant = $dette->montant - $montantPaye;
 
             if ($montantRestant > 0) {
                 $montantTotalRestant += $montantRestant;
             }
         }
         return $montantTotalRestant;
     }
 }
 