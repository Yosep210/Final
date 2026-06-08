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
        Schema::table('reward_points', function (Blueprint $table) {
            $table->foreignId('product_order_id')
                ->nullable()
                ->after('member_id')
                ->constrained('product_orders')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reward_points', function (Blueprint $table) {
            $table->dropForeign(['product_order_id']);
            $table->dropColumn('product_order_id');
        });
    }
};
