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
        Schema::create('thre_d_numbers', function (Blueprint $table) {
           $table->id();
            $table->bigInteger('three_d_ledger_id');
            $table->string('number');
            $table->bigInteger('limit_amount');
            $table->bigInteger('current_amount')->default(0);
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
        Schema::dropIfExists('thre_d_numbers');
    }
};