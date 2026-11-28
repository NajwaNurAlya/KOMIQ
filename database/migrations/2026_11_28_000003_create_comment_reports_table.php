<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comment_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('comment_id')->constrained('comments')->cascadeOnDelete();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->string('reason', 100)->default('Komentar bermasalah');
            $table->enum('status', ['pending', 'resolved'])->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
            $table->unique(['comment_id', 'reporter_id']);
        });
    }

    public function down(): void { Schema::dropIfExists('comment_reports'); }
};
