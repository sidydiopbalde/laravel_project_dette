<?php

namespace App\Providers;

use App\Events\UserCreated;
use App\Listeners\UploadImageListener;
use App\Listeners\SendEmailListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserCreated::class => [
            UploadImageListener::class,
            SendEmailListener::class,
        ],
        \App\Events\UsersCreated::class => [
            \App\Jobs\UploadUserPhotoJob::class,
        ],
    ];

    public function boot()
    {
        //
    }
}
