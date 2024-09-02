<?php

namespace Database\Seeders;

use App\Models\Dette;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DetteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        Dette::create([
            'client_id' => 1,
            'montant' => 500.00,
            'montant_due' => 500.00,
            'date' => '2024-09-01'
        ]);

        Dette::create([
            'client_id' => 2,
            'montant' => 1500.00,
            'montant_due' => 1500.00,
            'date' => '2024-09-02'
        ]);
    }
}
