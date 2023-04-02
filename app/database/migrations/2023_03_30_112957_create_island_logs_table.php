<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('island_logs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('turn_id');
            $table->bigInteger('island_id')->index();
            $table->json('log');
            $table->datetime('created_at');

            $table->index('created_at');
            $table->unique(['turn_id', 'island_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('island_logs');
    }
};
