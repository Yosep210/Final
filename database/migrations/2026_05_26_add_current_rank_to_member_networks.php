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
        // Add current_rank column to member_networks if it doesn't exist
        try {
            Schema::table('member_networks', function (Blueprint $table) {
                $table->string('current_rank')->nullable()->default('member')->after('rank');
            });
        } catch (Exception $e) {
            // Column may already exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        try {
            Schema::table('member_networks', function (Blueprint $table) {
                $table->dropColumn('current_rank');
            });
        } catch (Exception $e) {
            // Column may not exist
        }
    }
};
