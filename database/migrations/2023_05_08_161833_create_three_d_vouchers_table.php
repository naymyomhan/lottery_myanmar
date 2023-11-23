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
        Schema::create('three_d_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('vouchers_code')->unique()->nullable();
            $table->bigInteger('user_id');
            $table->bigInteger('three_d_ledger_id');
            $table->bigInteger('total_amount');
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
        Schema::dropIfExists('three_d_vouchers');
    }
};