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
            $table->bigInteger('island_id')->index();
            $table->integer('development_points')->index();
            $table->integer('population');
            $table->integer('funds');
            $table->integer('foods');
            $table->integer('resources');
            $table->integer('funds_production_number_of_people');
            $table->integer('foods_production_number_of_people');
            $table->integer('resources_production_number_of_people');
            $table->string('environment', 32);
            $table->integer('area');
            $table->timestamps();
            $table->dropColumn('updated_at');

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
        Schema::dropIfExists('island_statuses');
    }
};
