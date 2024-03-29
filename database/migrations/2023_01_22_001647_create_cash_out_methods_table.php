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
        if (!Schema::hasTable('cash_out_methods')) {
            Schema::create('cash_out_methods', function (Blueprint $table) {
                $table->id();
                $table->string("payment_name");
                $table->text("image_name");
                $table->text("image_path");
                $table->text("image_location");
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cash_out_methods');
    }
};