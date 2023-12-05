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
        Schema::create('thre_d_winners', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('three_d_ledger_id');
            $table->bigInteger('result_id');
            $table->bigInteger('user_id');
            $table->bigInteger('bet_id');
            $table->integer('type');
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
        Schema::dropIfExists('thre_d_winners');
    }
};