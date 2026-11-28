<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->boolean('is_reviewed')->default(false)->after('is_hidden');
            $table->foreignId('reviewed_by')->nullable()->after('is_reviewed')->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
        });

        DB::table('comments')->update(['is_reviewed' => true, 'reviewed_at' => now()]);
        DB::table('comments')->where('content', 'like', 'Komentar demo%')->update(['is_reviewed' => false, 'reviewed_by' => null, 'reviewed_at' => null]);
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn(['is_reviewed', 'reviewed_at']);
        });
    }
};
