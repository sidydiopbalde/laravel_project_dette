<?php

namespace App\Providers;
use App\Repository\ArticleRepository;
use App\Repository\ArticleRepositoryImpl;
use App\Repository\ClientRepositoryImpl;
use App\Services\ArticleService;
use App\Services\ArticleServiceImpl;
use App\Services\ClientServiceImpl;
use App\Services\DetteServiceImpl;
use App\Repository\DetteRepositoryImpl;
use App\Repository\UserRepository;
use App\Repository\UserRepositoryImpl;
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
    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
