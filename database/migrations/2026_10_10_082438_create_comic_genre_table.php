<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comic_genre', function (Blueprint $table) {
            $table->foreignId('comic_id')->constrained('comics')->cascadeOnDelete();
            $table->foreignId('genre_id')->constrained('genres')->cascadeOnDelete();
            $table->primary(['comic_id', 'genre_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comic_genre');
    }
};