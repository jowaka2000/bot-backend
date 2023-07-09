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
            $table->string('app_name');
            $table->string('page_name');
            $table->string('page_id');
            $table->longText('access_token')->nullable();
            $table->boolean('active')->default(true);
            $table->json('history')->nullable();
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
