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
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('admin_id');
            $table->string("title");
            $table->text("slug");
            $table->text("description");
            $table->text("image_path");
            $table->text("image_name");
            $table->text("image_location");
            $table->text("url");
            $table->bigInteger("category")->default(0);
            $table->bigInteger("type")->default(0);
            $table->bigInteger("current_user_count")->default(0);
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
        Schema::dropIfExists('games');
    }
};