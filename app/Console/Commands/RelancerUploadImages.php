<?php

namespace App\Console\Commands;

use App\Jobs\RelancePhotoCloud;
use App\Jobs\RetryUploadImageJob;
use Illuminate\Console\Command;

class RelancerUploadImages extends Command
{
    protected $signature = 'images:relancer';
    protected $description = 'Relance l\'upload des images non sauvegardées dans le cloud';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Dispatch le job qui relance l'upload des images
        RetryUploadImageJob::dispatch();

        $this->info('Job de relance des uploads d\'images lancé avec succès.');
    }
}
