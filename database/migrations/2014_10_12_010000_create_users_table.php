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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->foreign('agent_id')->references('id')->on('agents')->onDelete('set null');
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('password');
            $table->string('refer_code')->unique()->nullable();
            $table->timestamp('verify_code_send_at')->nullable();
            $table->integer('verify_code_send_count')->default(0);

            $table->integer('verify_code')->nullable();

            $table->integer('verify_attempt_count')->default(0);
            $table->timestamp('last_verify_attempt_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();

            $table->string('profile_picture_path')->nullable();
            $table->string('profile_picture_name')->nullable();
            $table->string('profile_picture_location')->nullable();
            $table->string('firebase_token')->nullable();
            $table->boolean('active')->default(false);
            $table->timestamp('last_message_send_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};