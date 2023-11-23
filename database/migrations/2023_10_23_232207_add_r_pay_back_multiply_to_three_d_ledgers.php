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
        Schema::table('three_d_ledgers', function (Blueprint $table) {
            // $table->decimal('r_pay_back_multiply', 10, 2)->after('pay_back_multiply');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('three_d_ledgers', function (Blueprint $table) {
            // $table->dropColumn('r_pay_back_multiply');
        });
    }
};