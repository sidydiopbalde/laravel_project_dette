<?php
namespace App\Services;

// use App\Services\Contracts\IMongoDB;
use App\Services\IMongoDB;
use MongoDB\Client;

class MongoDBServiceImpl implements MongoDBService
{
    protected $client;
    public function __construct()
    {
        $this->client = new Client(env('MONGO_URI'));
    }

    public function getClient(): Client
    {
        return $this->client;
    }
}