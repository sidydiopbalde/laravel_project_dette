<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dette_article', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dette_id')->constrained('dettes')->onDelete('cascade');
            $table->foreignId('article_id')->constrained('articles')->onDelete('cascade');
            $table->integer('qte_vente'); // Quantité vendue
            $table->decimal('prix_vente', 10, 2); // Prix de vente par article
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dette_article');
    }
};
