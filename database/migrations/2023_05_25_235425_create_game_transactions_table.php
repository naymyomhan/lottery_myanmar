<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGameTransactionsTable extends Migration
{
    public function up()
    {
        Schema::create('game_transactions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('player_id');
            $table->unsignedBigInteger('banker_id');
            $table->integer('bet_amount');
            $table->integer('banker_amount');
            $table->integer('tax');
            $table->enum('type', ['Player', 'Banker']);
            $table->timestamps();

            $table->foreign('player_id')->references('id')->on('users');
            $table->foreign('banker_id')->references('id')->on('users');
        });
    }

    public function down()
    {
        Schema::dropIfExists('game_transactions');
    }
}