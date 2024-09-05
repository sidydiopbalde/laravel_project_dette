<?php

namespace App\Providers;

use App\Events\UserCreated;
use App\Listeners\UploadImageListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        UserCreated::class => [
            UploadImageListener::class,
        ],
    ];

    public function boot()
    {
        //
    }
}
