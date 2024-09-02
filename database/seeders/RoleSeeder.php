<?php
use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    public function run()
    {
        $roles = ['Admin', 'Boutiquier', 'Client'];

        foreach ($roles as $role) {
            Role::create(['libelle' => $role]);
        }
    }
}
