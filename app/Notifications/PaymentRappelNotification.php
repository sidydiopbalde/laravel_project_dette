<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\SmsMessage;

class PaymentRappelNotification extends Notification
{
    protected $montantRestant;

    public function __construct($montantRestant)
    {
        $this->montantRestant = $montantRestant;
    }

    public function via($notifiable)
    {
        return ['sms'];
    }

    public function toSms($notifiable)
    {
        return "Bonjour {$notifiable->nom}, il vous reste un montant de {$this->montantRestant} à payer pour votre dette.";
    }
}
