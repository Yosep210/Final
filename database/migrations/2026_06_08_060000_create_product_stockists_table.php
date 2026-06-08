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
        Schema::create('product_stockist', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_member')->constrained('members')->onDelete('cascade');
            $table->unsignedBigInteger('id_source')->nullable();
            $table->string('source', 20)->nullable();
            $table->unsignedBigInteger('id_detail')->nullable();
            $table->foreignId('product')->constrained('products')->onDelete('cascade');
            $table->string('varian')->nullable();
            $table->integer('qty');
            $table->decimal('price', 14, 2)->default(0);
            $table->decimal('total', 14, 2)->default(0);
            $table->string('form', 50)->nullable();
            $table->string('type', 5); // IN or OUT
            $table->tinyInteger('status')->default(1);
            $table->text('description')->nullable();
            $table->timestamp('datecreated')->useCurrent();
            $table->timestamps();

            $table->index('id_member');
            $table->index('product');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_stockist');
    }
};
