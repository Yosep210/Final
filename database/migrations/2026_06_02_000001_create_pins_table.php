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
        Schema::create('pins', function (Blueprint $table) {
            $table->id();
            $table->string('serial_number')->unique()->index();
            $table->string('pin_code')->index();
            $table->enum('status', ['unused', 'used'])->default('unused');
            $table->foreignId('owner_id')->nullable()->constrained('members')->onDelete('set null');
            $table->foreignId('activated_member_id')->nullable()->constrained('members')->onDelete('set null');
            $table->timestamp('activated_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pins');
    }
};
