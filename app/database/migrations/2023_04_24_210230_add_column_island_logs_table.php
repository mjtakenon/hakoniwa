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
        Schema::table('island_logs', function (Blueprint $table) {
            $table->string('visibility',32)->after('log')->default(\App\Services\Hakoniwa\Log\LogVisibility::VISIBILITY_PUBLIC);
            $table->index(['turn_id', 'visibility'], 'island_logs_turn_id_visibility_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('island_logs', function (Blueprint $table) {
            $table->dropIndex('island_logs_turn_id_visibility_index');
            $table->dropColumn('visibility');
        });
    }
};
