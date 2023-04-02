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
        Schema::create('island_bbs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('turn_id');
            $table->bigInteger('island_id')->index();
            $table->bigInteger('contributors_island_id');
            $table->string('contents', 255);
            $table->datetime('created_at');
            $table->datetime('updated_at');
            $table->softDeletes();

            $table->index('created_at');
            $table->index(['turn_id', 'island_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('island_bbs');
    }
};
