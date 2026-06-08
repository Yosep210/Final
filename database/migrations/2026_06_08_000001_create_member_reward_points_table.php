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
        Schema::create('member_reward_points', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')
                ->constrained('members')
                ->onDelete('cascade');
            $table->string('type', 50)->default('reward_ro');
            $table->smallInteger('period')->default(0);
            $table->date('period_start')->nullable();
            $table->date('period_end')->nullable();
            $table->decimal('point_left', 14, 2)->default(0);
            $table->decimal('point_right', 14, 2)->default(0);
            $table->decimal('point_qualified', 14, 2)->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();

            $table->unique(['member_id', 'type', 'period'], 'unique_member_reward_point_period');
            $table->index('member_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_reward_points');
    }
};
