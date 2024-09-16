<?php

namespace App\Console;

use App\Console\Commands\RelancerUploadImages;
use App\Jobs\ArchiveSoldeJob;
use App\Jobs\ArchiveSoldesDettesJob;
use App\Jobs\SendDettesSmsJob;
use App\Jobs\SendPaymentRappel;
use App\Jobs\SmsClientsWithDetteJob;
use App\Models\Client;
use App\Models\Paiement;
use App\Notifications\DebtReminderNotification;
use App\Services\SmsNotificationService;
use App\Services\SmsNotificationsService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
   
    protected function schedule(Schedule $schedule)
    {
        // $schedule->job(new SendDettesSmsJob)->weeklyOn(5, '14:00'); 
    
         //$schedule->job(new ArchiveSoldesDettesJob)->everyMinute();
         // $schedule->command('images:retry-upload')->everyTwoMinutes();

        //$schedule->job(new SendDettesSmsJob(app()->make('App\Services\SmsService')))->everyMinute();
       //$schedule->job(new SmsClientsWithDetteJob())->everyMinute();

      // $schedule->job(new ArchiveSoldeJob)->everyMinute();

    //    $schedule->call(function () {
    //     app(SmsNotificationsService::class)->notifyClientsWithDebts();
    // })->everyMinute(); 
     $schedule->job(new SendPaymentRappel)->everyMinute();
    // Planifier l'envoi des notifications chaque vendredi à 14h
    // $schedule->call(function () {
    //     $clients = Client::with('dettes')->get();

    //     foreach ($clients as $client) {
    //         $montantTotalRestant = 0;

    //         foreach ($client->dettes as $dette) {
    //             $montantPaye = Paiement::where('dette_id', $dette->id)->sum('montant');
    //             $montantRestant = $dette->montant - $montantPaye;

    //             if ($montantRestant > 0) {
    //                 $montantTotalRestant += $montantRestant;
    //             }
    //         }

    //         if ($montantTotalRestant > 0) {
    //             $message = "Bonjour {$client->surnom}, il vous reste un total de {$montantTotalRestant} à payer pour vos dettes.";
    //             $client->notify(new DebtReminderNotification($message));
    //             Log::info("Notification envoyée à {$client->surnom} pour un montant de {$montantTotalRestant}");
    //         }
    //     }
    // })->everyMinute();
    }
   
    

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
