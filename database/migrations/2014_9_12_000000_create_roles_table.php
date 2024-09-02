<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // Importation de la classe DB
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('libelle')->unique(); // Nom du rôle (par exemple: ADMIN, Boutiquier, Client)
            $table->timestamps();
        });

        // Ajouter la colonne role_id dans la table users
        // Schema::table('users', function (Blueprint $table) {
        //     $table->unsignedBigInteger('role_id')->nullable();
        //     $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
         
    DB::table('roles')->insert([
            ['libelle' => 'Admin'],
            ['libelle' => 'Boutiquier'],
            ['libelle' => 'Client']
        ]);
        
    }

    public function down(): void
    {
        // Schema::table('users', function (Blueprint $table) {
        //     $table->enum('role', ['Admin', 'Boutiquier', 'Client'])->default('Boutiquier');
        //     $table->dropForeign(['role_id']);
        //     $table->dropColumn('role_id');
        // });

        Schema::dropIfExists('roles');
    }
};
