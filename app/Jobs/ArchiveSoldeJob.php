<?php

namespace App\Jobs;

use App\Models\Dette;
use App\Services\ArchiveService;
use App\Services\MongoServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Services\ArchiveServiceFactory;
class ArchiveSoldeJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Exécuter le job.
     *
     * @return void
     */
    public function handle()
    {
        try {
                $dettes = Dette::statut('Solde')->get();
            if ($dettes->isEmpty()) {
                Log::info('Aucune dette correspondant aux critères trouvée à archiver.');
                return;
            }
            $archiveService = ArchiveServiceFactory::create();
            // dd($archiveService);
            foreach ($dettes as $dette) {
                $archiveService->archiveDette($dette);
                // app(ArchiveService::class)->archiveDette($dette); // Appel du service d'archivage

                // Supprimer ou marquer la dette comme archivée dans la base SQL
                // $dette->delete();
            }

            Log::info('Les dettes dont le montant total des paiements est égal au montant de la dette ont été archivées avec succès dans MongoDB et supprimées de PostgreSQL.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'archivage des dettes : ' . $e->getMessage());
        }
    }
}