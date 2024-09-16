<?php

namespace App\Providers;

use App\Services\ArchiveService;
use Illuminate\Support\ServiceProvider;
use App\Services\MongoService;
use App\Services\FirebaseService;
use App\Services\ArchiveServiceCommunInterface;

class ArchiveServiceProvider extends ServiceProvider
{
    /**
     * Enregistrer les services dans le conteneur de services.
     */
    public function register()
    {
        // Lier l'interface à une implémentation en fonction de l'environnement
        $this->app->bind(ArchiveServiceCommunInterface::class, function ($app) {
            $serviceType = env('ARCHIVE_SERVICE', 'mongoDB'); // Par défaut MongoDB
            if ($serviceType === 'firebase') {
                return $app->make(FirebaseService::class);
            }
            return $app->make(ArchiveService::class);
        });
    }
}
