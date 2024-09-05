<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Jobs\UploadImageJob;
use Illuminate\Contracts\Queue\ShouldQueue;

class UploadImageListener implements ShouldQueue
{
    public function handle(UserCreated $event)
    {
        // Dispatch le Job pour uploader l'image
        UploadImageJob::dispatch($event->photo, $event->user->id);
    }
}

