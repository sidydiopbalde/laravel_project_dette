<?php

namespace App\Console;

use App\Console\Commands\RelancerUploadImages;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    // protected $commands = [
    //     RelancerUploadImages::class,  // Enregistrer la commande ici
    // ];
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // Relancer les uploads d'images toutes les heures
        $schedule->command('images:retry-upload')->everyTwoMinutes();
    }
    
//     protected function schedule(Schedule $schedule)
//     {
//         // Relancer les uploads d'images toutes les minutes
//         $schedule->command('images:relancer')->everyMinute();
//     }

//     protected function schedule(Schedule $schedule)
// {
//     // Relancer les uploads d'images toutes les 5 minutes
//     $schedule->command('images:relancer')->everyFiveMinutes();
// }


// Cela exécutera les tâches planifiées dans Laravel toutes les minutes, et Laravel vérifiera quelles tâches doivent être exécutées à ce moment-là.
// * * * * * php /path/to/your/project/artisan schedule:run >> /dev/null 2>&1

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
