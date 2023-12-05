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
        Schema::create('topups', function (Blueprint $table) {
            $table->id();
            $table->string('topup_transaction_number')->nullable();
            $table->bigInteger('user_id');
            $table->bigInteger('admin_id')->nullable();
            $table->string('payment_method');
            $table->string('payment_account_name');
            $table->string('payment_account_number');
            $table->bigInteger('amount');
            $table->string('payment_transaction_number')->nullable();
            $table->boolean('success')->default(false);
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
        Schema::dropIfExists('topups');
    }
};
