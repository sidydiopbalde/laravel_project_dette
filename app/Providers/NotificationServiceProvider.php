<?php

namespace App\Providers;

use App\Channels\SmsChannel as BroadcastingSmsChannel;
use Illuminate\Support\ServiceProvider;
use Illuminate\Notifications\ChannelManager;
use App\Channels\SmsChannel;
use App\Models\Notification;
use App\Services\InfoBipServiceSms;
use App\Services\SmsService;
use App\Services\SmsServiceInterface;

class NotificationServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // parent::boot();
        $this->app->make(ChannelManager::class)->extend('sms', function ($app) {
            return new SmsChannel($app->make(SmsServiceInterface::class));
        });
       
    }
    public function register()
    {
        $this->app->singleton(SmsServiceInterface::class, function ($app) {
            $provider = env('SMS_PROVIDER', 'twilio'); // 'twilio' ou 'infobip'

            switch ($provider) {
                case 'infobip':
                    return new InfoBipServiceSms(env('INFOBIP_API_KEY'), env('INFOBIP_BASE_URL'));
                case 'twilio':
                default:
                    return new SmsService(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'), env('TWILIO_PHONE_NUMBER'));
            }
        });
    }
}

