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
        Schema::create('island_statuses', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('turn_id');
            $table->bigInteger('island_id');
            $table->integer('development_points');
            $table->integer('population');
            $table->integer('funds');
            $table->integer('foods');
            $table->integer('resources');
            $table->integer('funds_production_number_of_people');
            $table->integer('foods_production_number_of_people');
            $table->integer('resources_production_number_of_people');
            $table->integer('environment');
            $table->integer('area');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('island_statuses');
    }
};
