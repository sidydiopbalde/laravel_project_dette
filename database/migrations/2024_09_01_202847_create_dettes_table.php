<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dettes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->onDelete('cascade'); // Clé étrangère pour lier la dette au client
            $table->decimal('montant', 10, 2); // Montant de la dette
            $table->decimal('montant_due', 10, 2); // Montant de la dette
            $table->date('date')->default(DB::raw('CURRENT_DATE')); // Date de la dette par défaut à aujourd'hui
            $table->timestamps(); // Crée les colonnes created_at et updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dettes');
    }
};
