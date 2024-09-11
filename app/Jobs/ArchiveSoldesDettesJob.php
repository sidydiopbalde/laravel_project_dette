<?php

namespace App\Jobs;

use App\Models\Dette;
use App\Models\Paiement; // Pour accéder aux paiements
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\ArchiveService; // Service d'archivage
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArchiveSoldesDettesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct()
    {
        //
    }

    public function handle()
    {
        // Récupérer les dettes où la somme des paiements est égale au montant de la dette
          // Récupérer les dettes où la somme des paiements est égale au montant de la dette
          $dettes = Dette::statut('Solde')->get();
  
    //   $dettes = DB::select(DB::raw($query));

        Log::info($dettes);
        // Archiver chaque dette dans MongoDB via le service d'archivage
        foreach ($dettes as $dette) {
            app(ArchiveService::class)->archiveDette($dette); // Appel du service d'archivage

            // Supprimer ou marquer la dette comme archivée dans la base SQL
            $dette->delete();
        }
    }
}
