<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\RoleEnum;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('prenom');
            $table->string('nom');
            $table->string('login')->unique();
            $table->string('mail')->unique();
            $table->string('password');
            $table->enum('role', array_column(RoleEnum::cases(), 'value'))->default(RoleEnum::BOUTIQUIER->value);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('prenom'); // Obligatoire
            $table->string('nom'); // Obligatoire
            $table->string('login')->unique(); // Obligatoire et doit être unique
            $table->string('mail')->unique(); // Obligatoire et doit être unique
            $table->string('password'); // Obligatoire
            $table->enum('role', ['ADMIN', 'Boutiquier', 'Client'])->default('Boutiquier'); // Obligatoire avec une valeur par défaut
            $table->rememberToken(); // Optionnel
            $table->timestamps(); // Obligatoire, géré par Laravel
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

