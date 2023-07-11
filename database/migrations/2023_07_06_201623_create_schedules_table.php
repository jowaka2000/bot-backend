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
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id');
            $table->foreignId('app_id');
            $table->json('messageContent')->nullable();
            $table->longText('url')->nullable();
            $table->json('images')->nullable();
            $table->string('schedule');
            $table->string('imageScheduler')->nullable();
            $table->boolean('publishPost')->nullable();
            $table->boolean('active')->default(true);
            $table->string('status')->nullable();
            $table->integer('frequency')->default(0);
            $table->json('history')->nullable();
            $table->timestamp('last_posted')->nullable();
            $table->timestamp('next_to_post')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
