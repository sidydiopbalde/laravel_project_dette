<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\SendDebtNotificationJob;
use App\Services\SmsServiceInterface;

class SendWeeklyNotifications extends Command
{
    protected $signature = 'notifications:send-weekly';
    protected $description = 'Envoyer des notifications aux clients avec des dettes non soldées chaque fin de semaine.';

    protected $smsService;

    public function __construct(SmsServiceInterface $smsService)
    {
        parent::__construct();
        $this->smsService = $smsService;
    }

    public function handle()
    {
        // Dispatcher le Job pour envoyer les notifications
       // SendDebtNotificationJob::dispatch($this->smsService);
        
       // $this->info('Les notifications ont été envoyées avec succès.');
    }
}
