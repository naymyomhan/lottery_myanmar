<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameServersTable extends Migration
{
    public function up()
    {
        Schema::create('game_servers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->text('ads');
            $table->integer('balance');
            $table->tinyInteger('status')->default(0);
            $table->string('app_version')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('game_servers');
    }
}