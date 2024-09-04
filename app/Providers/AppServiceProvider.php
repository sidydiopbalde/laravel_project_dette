<?php

namespace App\Providers;

use App\Repository\ArticleRepository;
use App\Repository\ArticleRepositoryImpl;
use App\Repository\ClientRepository;
use App\Repository\ClientRepositoryImpl;
use App\Services\ArticleService;
use App\Services\ArticleServiceImpl;
use App\Services\ClientService;
use App\Services\ClientServiceImpl;
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
        // $this->app->singleton(ClientService::class, ClientServiceImpl::class);

        // Enregistrement du nom pour la facade
        $this->app->singleton('Client_repository',function($app){
            return new ClientRepositoryImpl();
        });

        $this->app->singleton('Client_service',function($app){
            return new ClientServiceImpl();
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
