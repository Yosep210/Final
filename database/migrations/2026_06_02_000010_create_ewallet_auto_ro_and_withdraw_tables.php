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
        Schema::create('ewallet_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')
                ->constrained('members')
                ->onDelete('cascade');
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('source', 50)->nullable();
            $table->string('category', 50)->nullable();
            $table->decimal('nominal', 14, 2)->default(0);
            $table->decimal('percent', 5, 2)->default(0);
            $table->decimal('autoro', 14, 2)->default(0);
            $table->decimal('tax', 14, 2)->default(0);
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('type', 10)->default('IN'); // IN, OUT
            $table->integer('status')->default(1);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('member_id');
            $table->index('type');
            $table->index('source');
        });

        Schema::create('auto_ro_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')
                ->constrained('members')
                ->onDelete('cascade');
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('source', 50)->nullable();
            $table->decimal('nominal', 14, 2)->default(0);
            $table->decimal('percent', 5, 2)->default(0);
            $table->decimal('amount', 14, 2)->default(0);
            $table->integer('status')->default(1);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('member_id');
        });

        Schema::create('withdrawals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')
                ->constrained('members')
                ->onDelete('cascade');
            $table->string('type', 50)->nullable();
            $table->string('bank_name', 150)->nullable();
            $table->string('bank_code', 50)->nullable();
            $table->string('account_number', 100)->nullable();
            $table->string('account_holder', 150)->nullable();
            $table->decimal('nominal', 14, 2)->default(0);
            $table->decimal('nominal_receipt', 14, 2)->default(0);
            $table->decimal('tax', 14, 2)->default(0);
            $table->decimal('auto_ro', 14, 2)->default(0);
            $table->decimal('admin_fund', 14, 2)->default(0);
            $table->integer('status')->default(0); // 0 = pending, 1 = success, 2 = failed
            $table->string('flip_id', 100)->nullable();
            $table->string('linkita_inquiry', 100)->nullable();
            $table->string('inquiry_status', 100)->nullable();
            $table->string('linkita_pay', 100)->nullable();
            $table->string('payment_status', 100)->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->string('confirmed_by', 100)->nullable();
            $table->timestamps();

            $table->index('member_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('withdrawals');
        Schema::dropIfExists('auto_ro_logs');
        Schema::dropIfExists('ewallet_logs');
    }
};
