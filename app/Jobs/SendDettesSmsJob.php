<?php
// namespace App\Jobs;

// use App\Services\SmsService; // Service pour envoyer les SMS
// use App\Models\Client;
// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Bus\Dispatchable;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Queue\SerializesModels;
// use Illuminate\Support\Facades\Log;

// class SendDettesSmsJob implements ShouldQueue
// {
//     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//     protected $service;
//     public function __construct(SmsService $service)
//     {
//         $this->service = $service;
//     }

//     public function handle()
//     {
//         // Récupérer les clients ayant des dettes
//         // $clients = Client::whereHas('dettes', function ($query) {
//         //     $query->where('montant', '>', 0); // Clients ayant encore des dettes
//         // })->get();
//             // Log::info('salut');
//         // // Parcourir chaque client pour envoyer un SMS
//         // foreach ($clients as $client) {
//         //     $totalDettes = $client->dettes->sum('montant');
//             $message = "Bonjour {Sidy Diop Balde}, votre total de dettes est de {5000}.";

//             // Appeler le service d'envoi de SMS (SmsService)
//             // dd($$message);
//             $tele="+221784316538";
//            $this->service->sendSms($tele, $message);
//         // }
//     }
// }


namespace App\Jobs;

use App\Models\Client;
use App\Models\Dette;
use App\Services\SmsService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SendDettesSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }
    public function handle()
    {
        $this->smsService->notifyClientsWithDebts();
    }
   
}