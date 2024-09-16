<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Notification;
use App\Models\Paiement;
use Illuminate\Support\Facades\Http;
use App\Services\Contracts\SMSInterface;
use App\Services\SmsServiceInterface;

class InfoBipServiceSms implements SmsServiceInterface
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct(string $apiKey, string $baseUrl)
    {
        $this->apiKey = $apiKey;
        $this->baseUrl = $baseUrl;
    }

    public function sendSms(string $to, string $message): bool
    {
        $response = Http::withHeaders([
            'Authorization' => "App {$this->apiKey}",
            'Content-Type' => 'application/json',
        ])->post("{$this->baseUrl}/sms/2/text/single", [
            'from' => 'VotreBoutique',
            'to' => "+221784316538",
            'text' => $message,
        ]);

        return $response->successful();
    }
    public function notifyClientsWithDebts()
    {
        $clients = Client::with(['dettes'])->get();
        foreach ($clients as $client) {
            $montantTotalRestant = 0;
    
            foreach ($client->dettes as $dette) {
                // Calculer le montant payé pour chaque dette
                $montantPaye = Paiement::where('dette_id', $dette->id)->sum('montant');
                $montantRestant = $dette->montant - $montantPaye;
    
                if ($montantRestant > 0) {
                    // Ajouter au montant total restant du client
                    $montantTotalRestant += $montantRestant;
                }
            }
            // Envoyer un SMS uniquement si le client a un montant restant à payer
            if ($montantTotalRestant > 0) {
                // Construire le message à envoyer
                $message = "Bonjour {$client->surnom}, il vous reste un total de {$montantTotalRestant} à payer pour vos dettes.";
    
                // Dispatcher le Job pour envoyer le SMS
                $this->sendSms("+221784316538", $message);
                  // Enregistrer la notification en base de données
               
            }
        }
    }
}