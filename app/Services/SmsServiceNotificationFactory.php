<?php
// App\Services\SmsServiceFactory.php
namespace App\Services;

use App\Services\SmsNotificationServiceInterface;
use App\Services\SmsService;
use App\Services\InfoBipServiceSms;
class SmsServiceNotificationFactory

{
    public static function make(): SmsNotificationServiceInterface
    {
        $provider = env('SMS_PROVIDER', 'infobip'); // Défaut à Twilio si SMS_PROVIDER n'est pas défini

        switch ($provider) {
            case 'infobip':
                return new SmsInfobipNotificationService(env('INFOBIP_API_KEY'), env('INFOBIP_BASE_URL'));
            case 'twilio':
            default:
                return new SmsTwlioNotificationService();
        }
    }
}
