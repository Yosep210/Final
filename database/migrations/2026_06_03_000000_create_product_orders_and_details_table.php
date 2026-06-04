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
        Schema::create('product_orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice', 50)->nullable();
            $table->foreignId('member_id')
                ->constrained('members')
                ->onDelete('cascade');
            $table->unsignedBigInteger('stockist_id')->default(0);
            $table->unsignedBigInteger('espay_id')->default(0);
            $table->string('type_order', 50)->nullable();
            $table->text('products_json')->nullable();
            $table->text('meta_json')->nullable();
            $table->integer('status')->default(0)->comment('0=Review, 1=Confirmed, 2=Done, 4=Cancelled');
            $table->decimal('point_sponsor', 14, 2)->default(0);
            $table->decimal('point_pairing', 14, 2)->default(0);
            $table->decimal('point_reward', 14, 2)->default(0);
            $table->decimal('point_stockist', 14, 2)->default(0);
            $table->decimal('total_bv', 14, 2)->default(0);
            $table->integer('total_qty')->default(0);
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('shipping', 14, 2)->default(0);
            $table->integer('unique_code')->default(0);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('shipping_discount', 14, 2)->default(0);
            $table->decimal('fee', 14, 2)->default(0);
            $table->decimal('ppn', 14, 2)->default(0);
            $table->decimal('handling_fee', 14, 2)->default(0);
            $table->decimal('insurance_fee', 14, 2)->default(0);
            $table->decimal('additional_cost', 14, 2)->default(0);
            $table->decimal('autoro', 14, 2)->default(0);
            $table->decimal('total_checkout', 14, 2)->default(0);
            $table->decimal('total_payment', 14, 2)->default(0);
            $table->integer('payment_remain')->default(0);
            $table->decimal('total_omzet', 14, 2)->default(0);
            $table->decimal('voucher', 14, 2)->default(0);
            $table->decimal('saldo_eproduct', 14, 2)->default(0);
            $table->decimal('saldo_eshipping', 14, 2)->default(0);
            $table->decimal('saldo_eshipping_subsidy', 14, 2)->default(0);
            $table->string('payment_method', 100)->nullable();
            $table->string('payment_shipping_method', 50)->default('transfer');
            $table->tinyInteger('payment_shipping_status')->default(1);
            $table->string('bank_code', 50)->nullable();
            $table->string('account_number', 100)->nullable();
            $table->string('shipping_method', 100)->nullable();
            $table->string('shipping_courier', 100)->nullable();
            $table->string('shipping_service', 100)->nullable();
            $table->text('shipping_address')->nullable();
            $table->timestamps();

            $table->index('member_id');
            $table->index('status');
            $table->index('invoice');
        });

        Schema::create('product_order_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_order_id')
                ->constrained('product_orders')
                ->onDelete('cascade');
            $table->foreignId('member_id')
                ->constrained('members')
                ->onDelete('cascade');
            $table->unsignedBigInteger('product_package_id')->default(0);
            $table->unsignedBigInteger('product_id')->default(0);
            $table->unsignedBigInteger('varian_id')->default(0);
            $table->string('type', 50)->nullable();
            $table->integer('weight')->default(0);
            $table->decimal('point', 14, 2)->default(0);
            $table->decimal('bv', 14, 2)->default(0);
            $table->decimal('omzet', 14, 2)->default(0);
            $table->decimal('price', 14, 2)->default(0);
            $table->decimal('price_cart', 14, 2)->default(0);
            $table->decimal('additional_cost', 14, 2)->default(0);
            $table->integer('qty')->default(0);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('subtotal_bv', 14, 2)->default(0);
            $table->decimal('subtotal_omzet', 14, 2)->default(0);
            $table->decimal('subtotal_weight', 14, 2)->default(0);
            $table->decimal('subtotal_cost', 14, 2)->default(0);
            $table->timestamps();

            $table->index('product_order_id');
            $table->index('member_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_order_details');
        Schema::dropIfExists('product_orders');
    }
};
