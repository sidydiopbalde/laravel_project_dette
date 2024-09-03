<?php

namespace App\Providers;

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\UploadServiceImpl;

class ImageUploadServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('imageUploadService', function ($app) {
            return new UploadServiceImpl();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
