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
        Schema::table('island_histories', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->after('island_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('island_histories', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
