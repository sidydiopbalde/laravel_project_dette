<?php

namespace App\Services;

class ArchiveServiceFactory
{
    public static function create(): ArchiveServiceCommunInterface
    {
        $service = env('ARCHIVE_SERVICE', 'mongodb'); // Par défaut, on utilise MongoDB

        if ($service === 'firebase') {
            return app(FirebaseService::class);
        }

        return app(MongoDBService::class);
    }
}
