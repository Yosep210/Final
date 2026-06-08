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
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')
                ->constrained('members')
                ->onDelete('cascade');
            $table->foreignId('reward_config_id')
                ->constrained('reward_configs')
                ->onDelete('cascade');
            $table->string('type', 50)->nullable();
            $table->decimal('point_qualified', 14, 2)->default(0);
            $table->decimal('point_left', 14, 2)->default(0);
            $table->decimal('point_right', 14, 2)->default(0);
            $table->string('rank', 50)->nullable();
            $table->text('message')->nullable();
            $table->decimal('nominal', 14, 2)->default(0);
            $table->decimal('nominal_receipt', 14, 2)->default(0);
            $table->decimal('admin_fund', 14, 2)->default(0);
            $table->decimal('tax', 14, 2)->default(0);
            $table->string('bank_name', 150)->nullable();
            $table->string('bank_code', 50)->nullable();
            $table->string('account_number', 100)->nullable();
            $table->string('account_holder', 150)->nullable();
            $table->boolean('is_trip')->default(false);
            $table->boolean('claim')->default(false);
            $table->string('flip_id', 100)->nullable();
            $table->integer('status')->default(0)->comment('0=Pending, 1=Confirmed');
            $table->string('inquiry_status', 100)->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('claimed_at')->nullable();
            $table->string('confirm_by', 100)->nullable();
            $table->timestamps();

            $table->unique(['member_id', 'reward_config_id', 'type'], 'unique_member_reward_claim');
            $table->index('member_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
};
