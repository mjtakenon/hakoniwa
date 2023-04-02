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
        Schema::create('island_histories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('island_id')->index();
            $table->string('name', 32)->index();
            $table->string('owner_name', 32)->index();
            $table->datetime('created_at');

            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('island_histories');
    }
};
