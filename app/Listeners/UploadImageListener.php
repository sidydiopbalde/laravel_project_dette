<?php
namespace App\Listeners;

use App\Events\UserCreated;

use App\Jobs\UploadImageJob;

class UploadImageListener
{

    public function handle(UserCreated $event)
    {
          
        dispatch(new UploadImageJob($event->user, $event->photo));
    }
}