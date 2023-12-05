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
        Schema::create('mm_noon_bets', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('voucher_id');
            $table->bigInteger('user_id');
            $table->bigInteger('section_id');
            $table->bigInteger('mm_noon_number_id');
            $table->string("number");
            $table->integer('amount');
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
        Schema::dropIfExists('mm_noon_bets');
    }
};
