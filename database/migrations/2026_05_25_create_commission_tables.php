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
        Schema::create('commission_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')
                ->constrained('members')
                ->onDelete('cascade');

            // Commission type: binary, unilevel, generation, etc.
            $table->string('type')->default('binary');

            // Source of commission
            $table->string('source')->nullable(); // 'direct_sales', 'network_volume', etc.

            // Volume and calculation details
            $table->decimal('left_volume', 14, 2)->default(0);
            $table->decimal('right_volume', 14, 2)->default(0);
            $table->decimal('matched_volume', 14, 2)->default(0); // min(left, right)
            $table->decimal('gross_commission', 14, 2)->default(0);
            $table->decimal('tax_amount', 14, 2)->default(0);
            $table->decimal('net_commission', 14, 2)->default(0);

            // Commission rate applied
            $table->decimal('commission_rate', 5, 2)->default(0);
            $table->decimal('tax_rate', 5, 2)->default(0);

            // Current rank and month
            $table->string('member_rank')->nullable();
            $table->year('commission_year');
            $table->unsignedTinyInteger('commission_month');

            // Reference to related entities
            $table->foreignId('sponsored_by_id')
                ->nullable()
                ->constrained('members')
                ->onDelete('set null');

            $table->text('notes')->nullable();
            $table->boolean('is_paid')->default(false);
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            // Indexes
            $table->index('member_id');
            $table->index(['commission_year', 'commission_month']);
            $table->index('is_paid');
            $table->index(['member_id', 'commission_year', 'commission_month']);
            $table->index('created_at');
        });

        // Create commission_payouts table for tracking payments
        Schema::create('commission_payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')
                ->constrained('members')
                ->onDelete('cascade');

            $table->decimal('total_amount', 14, 2);
            $table->decimal('amount_paid', 14, 2)->default(0);
            $table->decimal('amount_remaining', 14, 2);

            // Payout period
            $table->year('payout_year');
            $table->unsignedTinyInteger('payout_month');

            // Status: pending, partial, completed, cancelled
            $table->enum('status', ['pending', 'partial', 'completed', 'cancelled'])->default('pending');

            $table->text('payment_method')->nullable();
            $table->string('transaction_ref')->nullable();
            $table->timestamp('payout_date')->nullable();

            $table->timestamps();

            $table->index('member_id');
            $table->index(['payout_year', 'payout_month']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_payouts');
        Schema::dropIfExists('commission_logs');
    }
};
