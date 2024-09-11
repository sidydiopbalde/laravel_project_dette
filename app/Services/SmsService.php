<?php
namespace App\Services;

use Twilio\Rest\Client as TwilioClient; // Renommer Twilio Client en TwilioClient
use App\Models\Client; // Renommer votre modèle Client en AppClient
use App\Models\Paiement;
use Illuminate\Support\Facades\DB;

class SmsService
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
        $this->twilio->messages->create($to, [
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
                $this->sendSms($client->telephone, $message);
            }
        }
    //     foreach($clients as $client) {
    //     foreach ($client->dettes as $dette) {

    //         // Calculer le montant payé pour chaque dette
    //         $montantPaye = Paiement::where('dette_id', $dette->id)->sum('montant');
    //         $montantRestant = $dette->montant - $montantPaye;

    //         if ($montantRestant > 0) {
    //             // Ajouter au montant total restant du client
    //             $montantTotalRestant += $montantRestant;
    //             $message = "Bonjour {$user->prenom } {$user->nom}, vous avez une dette totale de {$montantTotalRestant} FCFA. Merci de régulariser.";

    //             // Envoyer le SMS
    //             $this->sendSms($client->telephone, $message);
    //         }
    //     }
    // }
}
}
// namespace App\Services;

// use Illuminate\Support\Facades\Log;
// use Twilio\Rest\Client;

// class SmsService
// {
//     protected $twilio;

//     public function __construct()
//     {
//         $sid = "AC3ad4a00e75bfcf3a16c273192db708d7";
//         $token = "e1483959057fff6ed00119a76dd2e154";
//         // dd($sid, $token,env('TWILIO_PHONE_NUMBER'));
//         if (!$sid || !$token) {
//             throw new \Exception('Twilio credentials are not set.');
//         }
        
//         // Log::info('salut');
//         $this->twilio = new Client($sid, $token);
//         // dd($sid,$token,$this->twilio, env('TWILIO_PHONE_NUMBER'));
//     }
    
//     public function sendSms($to, $message)
//     {
//         // dd($to,$message);
//         try {
//             $this->twilio->messages->create($to, [
//                 'from' => "+12512442090",
//                 'body' => $message
//             ]);
//         } catch (\Exception $e) {
//             return $e->getMessage();
//         }

//         return true;
//     }
// }

