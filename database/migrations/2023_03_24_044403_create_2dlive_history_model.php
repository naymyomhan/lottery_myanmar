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
        Schema::create('2dlive_history_model', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('tdlive_id');
            $table->string('set');
            $table->string('value');
            $table->string('time');
            $table->string('twod');
            $table->string('raw');
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
        Schema::dropIfExists('2dlive_history_model');
    }
};