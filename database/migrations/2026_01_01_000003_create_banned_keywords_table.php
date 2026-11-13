<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('banned_keywords', function (Blueprint $table) {
            $table->id();
            $table->string('keyword')->unique();
            $table->enum('category', ['gambling', 'online_game', 'pornography', 'other'])->index();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banned_keywords');
    }
};
