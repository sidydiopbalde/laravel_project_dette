<?php
namespace App\Jobs;

use App\Services\SmsServiceFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SmsClientsWithDetteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle()
    {
        // Utiliser la factory pour obtenir le service approprié
        $smsService = SmsServiceFactory::make();
        $smsService->notifyClientsWithDebts();
    }
}
