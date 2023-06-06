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
        Schema::dropIfExists('island_bbs');
        Schema::create('island_bbs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('island_id')->index();
            $table->unsignedBigInteger('commenter_user_id');
            $table->unsignedBigInteger('commenter_island_id');
            $table->unsignedBigInteger('turn_id');
            $table->string('comment', 128);
            $table->string('visibility', 32);
            $table->datetime('created_at')->index();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('island_bbs');
        Schema::create('island_bbs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('turn_id');
            $table->unsignedBigInteger('island_id')->index();
            $table->unsignedBigInteger('contributors_island_id');
            $table->string('contents', 255);
            $table->datetime('created_at');
            $table->datetime('updated_at');
            $table->softDeletes();

            $table->index('created_at');
            $table->unique(['turn_id', 'island_id']);
        });
    }
};
