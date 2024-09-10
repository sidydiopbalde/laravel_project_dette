<?php

namespace App\Providers;

use App\Services\MongoDBService;
use App\Services\MongoDBServiceImpl;
use Illuminate\Support\ServiceProvider;
use MongoDB\Client;

class MongoDBServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Enregistrer MongoDB dans le conteneur de services
        // $this->app->singleton('mongodb', function ($app) {
        //     return (new Client(env('MONGO_URI')))->{env('MONGO_DATABASE')};
        // });
        $this->app->singleton(MongoDBService::class, MongoDBServiceImpl::class);
    }

    public function boot()
    {
        //
    }
}
