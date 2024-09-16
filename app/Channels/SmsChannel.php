<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;
use App\Services\SmsServiceInterface;

class SmsChannel
{
    protected $smsService;

    public function __construct(SmsServiceInterface $smsService)
    {
        $this->smsService = $smsService;
    }

    public function send($notifiable, Notification $notification)
    {
        // Vérification si le client a un numéro de téléphone
        if (! $notifiable->telephone) {
            return;
        }

        // Vérifier si la méthode toSms() existe dans la notification
        if (method_exists($notification, 'toSms')) {
            $message = $notification->toSms($notifiable);
            $this->smsService->sendSms($notifiable->telephone, $message);
        } else {
            throw new \Exception("La notification doit implémenter la méthode toSms.");
        }
    }
 
}



