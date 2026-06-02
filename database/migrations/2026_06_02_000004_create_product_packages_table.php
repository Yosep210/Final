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
        Schema::create('product_packages', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->nullable();
            $table->string('name');
            $table->string('slug');
            $table->string('type')->nullable(); // perdana, upgrade, ro, etc.
            $table->tinyInteger('hu')->default(1);
            $table->string('type_price')->nullable();
            $table->integer('total_item')->default(0);
            $table->integer('total_qty')->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);
            $table->double('bv')->default(0);
            $table->decimal('price', 14, 2)->default(0);
            $table->double('weight')->nullable(); // in Grams
            $table->double('sponsor_point')->default(0);
            $table->double('pairing_point')->default(0);
            $table->double('stockist_point')->default(0);
            $table->double('reward_point')->default(0);
            $table->string('image')->nullable();
            $table->text('description')->nullable();
            $table->boolean('show_order')->default(true);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['name', 'slug']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_packages');
    }
};
