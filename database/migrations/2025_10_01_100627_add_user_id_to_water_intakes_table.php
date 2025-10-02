<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('water_intakes', function (Blueprint $table) {
        // Ajouter la contrainte uniquement si la colonne existe déjà
        if (!Schema::hasColumn('water_intakes', 'user_id')) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        } else {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        }
    });
}

public function down(): void
{
    Schema::table('water_intakes', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
        // Ne supprime pas la colonne si elle existait déjà
    });
}

};
