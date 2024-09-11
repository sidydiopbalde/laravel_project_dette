<?php

namespace App\Console;

use App\Console\Commands\RelancerUploadImages;
use App\Jobs\ArchiveSoldesDettesJob;
use App\Jobs\SendDettesSmsJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
   
    protected function schedule(Schedule $schedule)
    {
        // $schedule->job(new SendDettesSmsJob)->weeklyOn(5, '14:00'); 
    
         //$schedule->job(new ArchiveSoldesDettesJob)->everyMinute();

        // $schedule->command('images:retry-upload')->everyTwoMinutes();
        $schedule->job(new SendDettesSmsJob(app()->make('App\Services\SmsService')))->everyMinute();
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
