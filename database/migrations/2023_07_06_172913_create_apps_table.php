<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('apps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('bot_type');
            $table->string('bot_name');
            $table->string('media_name')->nullable();
            $table->string('page_id')->nullable();
            $table->string('bot_user_id')->nullable();
            $table->string('bot_nickname')->nullable();
            $table->string('channel_link')->nullable();
            $table->string('bot_username')->nullable();
            $table->string('bot_accessToken')->nullable();
            $table->string('bot_link')->nullable();
            $table->string('telegram_bot_access_token')->nullable();
            $table->string('telegram_bot_username')->nullable();
            $table->json('history')->nullable();
            $table->longText('access_token')->nullable();
            $table->string('telegram_chat_id')->nullable();
            $table->boolean('active')->default(false);//user switch the app off
            $table->boolean('subscribed')->default(false);//if the user is subscribed
            $table->boolean('approved')->default(false);//admin adding username and password
            $table->boolean('activated')->default(false);//activated by user
            $table->timestamp('end_of_subscription')->nullable();
            $table->string('search_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apps');
    }
};
