<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToArticlesTable extends Migration
{
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->softDeletes(); // Ajoute la colonne 'deleted_at'
        });
    }

    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropSoftDeletes(); // Supprime la colonne 'deleted_at'
        });
    }
}
