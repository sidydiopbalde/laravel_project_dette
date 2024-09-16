<?php


namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Messages\TwilioMessage;
use App\Services\SmsServiceInterface;

class DebtReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $message;

    public function __construct($message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        // Vous pouvez spécifier les canaux via lesquels la notification doit être envoyée
        return ['database', 'sms'];
    }

    // Si vous utilisez un service personnalisé
    
    public function toDatabase($notifiable)
    {
        // Structure du message à stocker en base de données
        return [
            'message' => $this->message,
            'client_id' => $notifiable->id,
        ];
    }
    public function toArray($notifiable)
    {
        return [
            'message' => $this->message,
        ];
    }
    public function toSms($notifiable)
    {
        // Récupérer le service SMS (Twilio ou Infobip) via l'interface
        $smsService = app(SmsServiceInterface::class);

        // Envoyer le SMS
        $smsService->sendSms($notifiable->telephone, $this->message);
    }
    // Utilisation de Twilio pour envoyer le SMS
    // public function toTwilio($notifiable)
    // {
    //     return (new TwilioSmsNotification())
    //         ->content($this->message);
    // }

    // // Utilisation de Nexmo (pour Infobip) ou tout autre service
    // public function toNexmo($notifiable)
    // {
    //     return (new NexmoMessage())
    //         ->content($this->message);
    // }

}
