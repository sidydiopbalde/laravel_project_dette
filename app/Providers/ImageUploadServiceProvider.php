<?php

namespace App\Providers;

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\UploadServiceImpl;
use App\Services\ImageUploadService;
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
        $this->app->singleton('UploadCloudImageFacade', function ($app) {
            return new ImageUploadService();
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
