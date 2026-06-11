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
        Schema::create('reward_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')
                ->constrained('members')
                ->onDelete('cascade');
            $table->foreignId('product_order_id')
                ->nullable()
                ->constrained('product_orders')
                ->onDelete('set null');
            $table->string('package', 50);
            $table->string('type', 20)->default('ro');
            $table->integer('bv')->default(0);
            $table->decimal('point', 14, 2)->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();

            $table->index('member_id');
            $table->index('type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_points');
    }
};
