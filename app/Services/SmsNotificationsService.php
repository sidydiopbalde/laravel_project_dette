<?php
namespace App\Services;

class SmsNotificationsService
{
    protected $smsService;

    public function __construct()
    {
        // Utilise la factory pour déterminer quel service SMS utiliser
        $this->smsService = SmsServiceNotificationFactory::make();
    }

    public function notifyClientsWithDebts()
    {
        // Envoie les SMS via le service sélectionné (Twilio ou InfoBip)
        $this->smsService->notifyClientsWithDebts();
    }
}

// class SmsNotificationService
// {
//     protected $twilioService;
//     protected $infoBipService;

//     public function __construct(SmsService $twilioService, InfoBipServiceSms $infoBipService)
//     {
//         $this->twilioService = $twilioService;
//         $this->infoBipService = $infoBipService;
//     }

//     public function notifyClientsWithDebts()
//     {
//         $this->twilioService->notifyClientsWithDebts();
//         $this->infoBipService->notifyClientsWithDebts();
//     }
// }


// class SmsNotificationService
// {
//     protected $twilioService;
//     protected $infoBipService;

//     public function __construct(SmsService $twilioService, InfoBipServiceSms $infoBipService)
//     {
//         $this->twilioService = $twilioService;
//         $this->infoBipService = $infoBipService;
//     }

//     public function notifyClientsWithDebts()
//     {
//         try {
//             // Essayer d'envoyer via Twilio
//             $this->twilioService->notifyClientsWithDebts();
//         } catch (\Exception $e) {
//             // Si Twilio échoue, utiliser InfoBip
//             $this->infoBipService->notifyClientsWithDebts();
//         }
//     }
// }