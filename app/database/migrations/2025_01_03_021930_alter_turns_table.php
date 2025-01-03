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
        Schema::table('turns', function (Blueprint $table) {
            $table->datetime('deleted_at')->nullable()->default(null)->after('created_at');
            $table->index(['turn', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('turns', function (Blueprint $table) {
            $table->dropIndex('turns_turn_deleted_at_index');
            $table->dropColumn('deleted_at');
        });
    }
};
