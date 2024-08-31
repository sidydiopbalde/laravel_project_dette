<?php

namespace Database\Seeders;
use Database\Factories\ClientsFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Clients;
class ClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Clients::factory()->count(3)->create();
        Clients::factory()->count(3)->create(['user_id' => null]);
       // with user
        // ClientsFactory::factory()->withoutuser()->count(3)->create();
    }
}
