<?php

namespace App\Providers;

use App\Models\Paiement;
use App\Repository\ArticleRepository;
use App\Repository\ArticleRepositoryImpl;
use App\Repository\ClientRepositoryImpl;
use App\Services\ArticleService;
use App\Services\ArticleServiceImpl;
use App\Services\ClientServiceImpl;
use App\Services\DetteServiceImpl;
use App\Repository\DetteRepositoryImpl;
use App\Repository\PaiementRepository;
use App\Repository\PaiementRepositoryImpl;
use App\Repository\UserRepository;
use App\Repository\UserRepositoryImpl;
use App\Services\FirebaseService;
use App\Services\FirebaseServiceInterface;
use App\Services\PaiementService;
use App\Services\PaiementServiceImpl;
use App\Services\SmsService;
use App\Services\SmsServiceInterface;
use App\Services\UploadService;
use App\Services\UploadServiceImpl;
use App\Services\UserService;
use App\Services\UserServiceImpl;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        $this->app->bind(ArticleRepository::class, ArticleRepositoryImpl::class);
         $this->app->bind(ArticleService::class, ArticleServiceImpl::class);
         $this->app->bind(SmsServiceInterface::class, SmsService::class);
         $this->app->bind(UploadService::class, UploadServiceImpl::class);
         $this->app->bind(FirebaseServiceInterface::class, FirebaseService::class);
    

        // Enregistrement du nom pour la facade
        $this->app->singleton('Client_repository',function($app){
            return new ClientRepositoryImpl();
        });

        $this->app->singleton('Client_service',function($app){
            return new ClientServiceImpl();
        });


        $this->app->singleton('dette_service', function ($app) {
            return new DetteServiceImpl();
        });
        $this->app->singleton('dette_repository', function ($app) {
            return new DetteRepositoryImpl();
        });
        $this->app->singleton(UserRepository::class, function ($app) {
            return new UserRepositoryImpl();
        });

        $this->app->singleton(UserService::class, function ($app) {
            return new UserServiceImpl($app->make(UserRepository::class));
        });
        $this->app->singleton(PaiementRepository::class, function ($app) {
            return new PaiementRepositoryImpl();
        });

        $this->app->singleton(PaiementService::class, function ($app) {
            return new PaiementServiceImpl($app->make(PaiementRepository::class));
        });
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
