<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coin_transactions', function (Blueprint $table) {
            $table->string('order_id', 50)->nullable()->unique();
            $table->string('status', 20)->default('success');
            $table->integer('price')->nullable();
            $table->integer('balance_after')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('coin_transactions', function (Blueprint $table) {
            $table->dropColumn(['order_id', 'status', 'price']);
            $table->integer('balance_after')->nullable(false)->change();
        });
    }
};
