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
        Schema::create('promotion_top_ups', function (Blueprint $table) {
            $table->id();
             $table->bigInteger('admin_id');
             $table->bigInteger('user_id');
             $table->bigInteger('promotion_id');
             $table->integer('amount')->default(0);
             $table->string('refer_code');
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
        Schema::dropIfExists('promotion_top_ups');
    }
};