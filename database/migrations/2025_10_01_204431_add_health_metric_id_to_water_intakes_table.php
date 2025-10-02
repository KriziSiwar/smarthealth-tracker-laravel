<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  // database/migrations/xxxx_add_health_metric_id_to_water_intakes.php
public function up()
{
    Schema::table('water_intakes', function (Blueprint $table) {
        $table->foreignId('health_metric_id')
              ->nullable()
              ->constrained('health_metrics')
              ->onDelete('cascade');
    });
}
    /**
     * Reverse the migrations.
     */
     public function down()
    {
        Schema::table('water_intakes', function (Blueprint $table) {
            // Supprime la contrainte et la colonne en cas de rollback
            $table->dropForeign(['health_metric_id']);
            $table->dropColumn('health_metric_id');
        });
    }
};
