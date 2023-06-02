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
        Schema::create('island_achievements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('island_id')->index();
            $table->unsignedBigInteger('turn_id')->index();
            $table->string('type', 128)->index();
            $table->json('extra')->nullable();
            $table->datetime('created_at')->index();
            $table->datetime('updated_at')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('island_achievements');
    }
};
