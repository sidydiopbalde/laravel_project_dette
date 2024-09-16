<?php
namespace App\Services;

use Twilio\Rest\Client as TwilioClient;
use App\Models\Client;
use App\Models\Paiement;
use App\Models\Notification; // Assurez-vous d'importer le modèle Notification

class SmsTwlioNotificationService implements SmsNotificationServiceInterface
{
    protected $twilio;

    public function __construct()
    {
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_AUTH_TOKEN');
        $this->twilio = new TwilioClient($sid, $token);
    }

    public function sendSms($to, $message)
    {
        $this->twilio->messages->create($to, [
            'from' => env('TWILIO_PHONE_NUMBER'),
            'body' => $message,
        ]);
    }

    public function notifyClientsWithDebts()
    {
        $clients = Client::with(['dettes'])->get();
        foreach ($clients as $client) {
            $montantTotalRestant = 0;

            foreach ($client->dettes as $dette) {
                $montantPaye = Paiement::where('dette_id', $dette->id)->sum('montant');
                $montantRestant = $dette->montant - $montantPaye;

                if ($montantRestant > 0) {
                    $montantTotalRestant += $montantRestant;
                }
            }

            if ($montantTotalRestant > 0) {
                $message = "Bonjour {$client->surnom}, il vous reste un total de {$montantTotalRestant} à payer pour vos dettes.";
                
                // Envoyer le SMS
                $this->sendSms($client->telephone, $message);

                // Enregistrer la notification en base de données
                Notification::create([
                    'client_id' => $client->id,
                    'message' => $message,
                ]);
            }
        }
    }
}
