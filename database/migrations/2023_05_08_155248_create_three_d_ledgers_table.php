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
        Schema::create('three_d_ledgers', function (Blueprint $table) {
    $table->id();
    $table->date('target_date');
    $table->date('open_date');
    $table->time('open_at');
    $table->date('limit_date');
    $table->time('limit_time');
    $table->integer('pay_back_multiply');
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
        Schema::dropIfExists('three_d_ledgers');
    }
};