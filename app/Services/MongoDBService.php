<?php
namespace App\Services;

use Illuminate\Http\Request;
use MongoDB\Client;

interface MongoDBService
{
    public function getClient():Client;
}