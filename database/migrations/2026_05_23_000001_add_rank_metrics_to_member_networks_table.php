<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('member_networks', function (Blueprint $table) {
            $table->decimal('left_volume', 14, 2)->default(0)->after('rank');
            $table->decimal('right_volume', 14, 2)->default(0)->after('left_volume');
            $table->decimal('total_volume', 14, 2)->default(0)->after('right_volume');
            $table->unsignedSmallInteger('qualified_legs')->default(0)->after('total_volume');
            $table->string('current_rank')->nullable()->after('qualified_legs');
        });
    }

    public function down(): void
    {
        Schema::table('member_networks', function (Blueprint $table) {
            $table->dropColumn(['left_volume', 'right_volume', 'total_volume', 'qualified_legs', 'current_rank']);
        });
    }
};
