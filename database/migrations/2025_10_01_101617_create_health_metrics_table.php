<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;



return new class extends Migration
{
    // database/migrations/xxxx_create_health_metrics_table.php
public function up()
{
    Schema::create('health_metrics', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->float('weight_kg');
        $table->string('measurement');
        $table->date('measured_at');  // ← IMPORTANT: type DATE pour la jointure
        $table->timestamps();
        
        // Index pour optimiser les jointures
        $table->index(['user_id', 'measured_at']);
    });
}

    public function down(): void
    {
        Schema::dropIfExists('health_metrics');
    }
};

    /**
     * Reverse the migrations.
     */

