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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->nullable();
            $table->string('name');
            $table->string('slug');
            $table->string('type')->default('all');
            $table->string('varian')->nullable();
            $table->tinyInteger('hu')->default(1);
            $table->integer('bv')->default(0);
            $table->decimal('price_hpp', 14, 2)->default(0);
            $table->decimal('price', 14, 2)->default(0);
            $table->decimal('price_member', 14, 2)->default(0);
            $table->decimal('price_customer', 14, 2)->default(0);
            $table->double('sponsor_point')->default(0);
            $table->double('pairing_point')->default(0);
            $table->double('reward_point')->default(0);
            $table->double('stockist_point')->default(0);
            $table->decimal('reward_budget', 14, 2)->default(0);
            $table->double('weight')->nullable(); // in Grams
            $table->integer('stock')->default(0);
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('show_order')->default(true);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['name', 'slug', 'varian']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
