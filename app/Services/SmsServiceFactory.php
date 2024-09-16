<?php
// App\Services\SmsServiceFactory.php
namespace App\Services;

use App\Services\SmsServiceInterface;
use App\Services\SmsService;
use App\Services\InfoBipServiceSms;
class SmsServiceFactory

{
    public static function make(): SmsServiceInterface
    {
        $provider = env('SMS_PROVIDER', 'twilio'); // Défaut à Twilio si SMS_PROVIDER n'est pas défini

        switch ($provider) {
            case 'infobip':
                return new InfoBipServiceSms(env('INFOBIP_API_KEY'), env('INFOBIP_BASE_URL'));
            case 'twilio':
            default:
                return new SmsService();
        }
    }
}
