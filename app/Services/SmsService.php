<?php
namespace App\Services;

use Twilio\Rest\Client as TwilioClient; // Renommer Twilio Client en TwilioClient
use App\Models\Client; // Renommer votre modèle Client en AppClient
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;
use App\Models\Notification;
class SmsService implements SmsServiceInterface
{
    protected $twilio;

    public function __construct()
    {
        $sid = "AC3ad4a00e75bfcf3a16c273192db708d7";
         $token = "e1483959057fff6ed00119a76dd2e154";
        // $this->twilio = new TwilioClient(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
         $this->twilio = new TwilioClient($sid, $token);
    }

    public function sendSms($to, $message)
    {
        $this->twilio->messages->create("+221784316538", [
            'from' => "+12512442090",
            'body' => $message
        ]);
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
                $this->sendSms("221784316538", $message);
                  // Enregistrer la notification en base de données
                  Notification::create([
                    'client_id' => $client->id,
                    'message' => $message,
                ]);
            }
        }
   
    }
  
}


