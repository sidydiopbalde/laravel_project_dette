<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use RoleSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       $this->call([
        // ClientsSeeder::class,
        // UsersSeeder::class
        ArticleSeeder::class
       ]);
       $this->call([
         RoleSeeder::class,
        // PermissionSeeder::class,
        // ClientUserSeeder::class,
        // UserRoleSeeder::class,
        // UserPermissionSeeder::class,
        // UserArticleSeeder::class, // Uncomment this line if you want to seed User-Article relationships
       ]);
    }
}
