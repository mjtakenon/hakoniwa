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
            $table->integer('abandoned_turn')->after('area');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('island_statuses', function (Blueprint $table) {
            $table->dropColumn('abandoned_turn');
        });
    }
};
