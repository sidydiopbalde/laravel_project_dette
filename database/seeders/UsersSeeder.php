<?php

namespace Database\Seeders;

use Database\Factories\ClientsFactory;
use Database\Factories\UsersFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Users;
class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       
        Users::factory(count: 1)->admin()->create();
        Users::factory(count:1)->boutiquier()->create();
        
    }
}
