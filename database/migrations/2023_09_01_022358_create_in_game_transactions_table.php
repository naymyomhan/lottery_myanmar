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
        Schema::create('in_game_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('play_id');
            $table->integer('seat_one_id');
            $table->integer('seat_one_bet');
            $table->integer('seat_one_win');
            $table->integer('seat_one_lose');
            $table->text('seat_one_cards');
            $table->integer('seat_one_card_count');
            $table->integer('seat_one_zaa');
            $table->integer('seat_one_before');
            $table->integer('seat_one_after');

            $table->integer('seat_two_id');
            $table->integer('seat_two_bet');
            $table->integer('seat_two_win');
            $table->integer('seat_two_lose');
            $table->text('seat_two_cards');
            $table->integer('seat_two_card_count');
            $table->integer('seat_two_zaa');
            $table->integer('seat_two_before');
            $table->integer('seat_two_after');

            $table->integer('seat_three_id');
            $table->integer('seat_three_bet');
            $table->integer('seat_three_win');
            $table->integer('seat_three_lose');
            $table->text('seat_three_cards');
            $table->integer('seat_three_card_count');
            $table->integer('seat_three_zaa');
            $table->integer('seat_three_before');
            $table->integer('seat_three_after');

            $table->integer('seat_four_id');
            $table->integer('seat_four_bet');
            $table->integer('seat_four_win');
            $table->integer('seat_four_lose');
            $table->text('seat_four_cards');
            $table->integer('seat_four_card_count');
            $table->integer('seat_four_zaa');
            $table->integer('seat_four_before');
            $table->integer('seat_four_after');

            $table->integer('seat_five_id');
            $table->integer('seat_five_bet');
            $table->integer('seat_five_win');
            $table->integer('seat_five_lose');
            $table->text('seat_five_cards');
            $table->integer('seat_five_card_count');
            $table->integer('seat_five_zaa');
            $table->integer('seat_five_before');
            $table->integer('seat_five_after');

            $table->integer('seat_six_id');
            $table->integer('seat_six_bet');
            $table->integer('seat_six_win');
            $table->integer('seat_six_lose');
            $table->text('seat_six_cards');
            $table->integer('seat_six_card_count');
            $table->integer('seat_six_zaa');
            $table->integer('seat_six_before');
            $table->integer('seat_six_after');

            $table->integer('seat_seven_id');
            $table->integer('seat_seven_bet');
            $table->integer('seat_seven_win');
            $table->integer('seat_seven_lose');
            $table->text('seat_seven_cards');
            $table->integer('seat_seven_card_count');
            $table->integer('seat_seven_zaa');
            $table->integer('seat_seven_before');
            $table->integer('seat_seven_after');

            $table->integer('banker_id');
            $table->integer('banker_before');
            $table->integer('banker_after');
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
        Schema::dropIfExists('in_game_transactions');
    }
};
