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
        Schema::create('reward_configs', function (Blueprint $table) {
            $table->id();
            $table->string('type', 50);
            $table->string('reward');
            $table->decimal('nominal', 14, 2)->default(0);
            $table->integer('point')->default(0);
            $table->text('packages')->nullable();
            $table->string('rank')->nullable();
            $table->text('message')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_lifetime')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['type', 'reward', 'point', 'start_date', 'end_date', 'is_active'], 'unique_reward_config');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reward_configs');
    }
};
