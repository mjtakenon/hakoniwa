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
        Schema::table('island_statuses', function (Blueprint $table) {
            $table->index(['turn_id', 'development_points'], 'island_statuses_turn_id_development_points_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('island_statuses', function (Blueprint $table) {
            $table->dropIndex('island_statuses_turn_id_development_points_index');
        });
    }
};
