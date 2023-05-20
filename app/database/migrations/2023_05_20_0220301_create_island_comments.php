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
        Schema::create('island_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('island_id');
            $table->string('comment', 128);
            $table->datetime('created_at')->index();
            $table->softDeletes();

            $table->index(['island_id', 'deleted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('island_comments');
    }
};
