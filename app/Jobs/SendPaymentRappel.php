<?php

namespace App\Jobs;

use App\Models\Dette;
use App\Notifications\PaymentRappelNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Carbon\Carbon;

class SendPaymentRappel implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $today = Carbon::now();
        $dettes = Dette::where('date_echeance', '<', $today)
                       ->with('client') // Charge la relation client
                       ->get();

        foreach ($dettes as $dette) {
            $montantRestant = $dette->montantRestant();

            if ($montantRestant > 0) {
                $client = $dette->client;
                $client->notify(new PaymentRappelNotification($montantRestant));
            }
        }
    }
}
