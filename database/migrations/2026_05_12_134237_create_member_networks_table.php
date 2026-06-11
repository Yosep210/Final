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
        Schema::create('member_networks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->unique()->constrained('members')->onDelete('cascade');
            $table->foreignId('sponsored_id')->nullable()->index()->constrained('members')->onDelete('set null');
            $table->foreignId('parent_id')->nullable()->index()->constrained('members')->onDelete('set null');
            $table->enum('position', ['left', 'right'])->nullable();
            $table->string('path')->nullable()->index();
            $table->integer('generation')->default(0)->index();
            $table->integer('group')->default(0)->index();
            $table->integer('rank')->default(0)->index();
            $table->decimal('left_volume', 14, 2)->default(0);
            $table->decimal('right_volume', 14, 2)->default(0);
            $table->decimal('total_volume', 14, 2)->default(0);
            $table->unsignedSmallInteger('qualified_legs')->default(0);
            $table->string('current_rank')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->unique(['parent_id', 'position']);
            $table->index(['parent_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_networks');
    }
};
