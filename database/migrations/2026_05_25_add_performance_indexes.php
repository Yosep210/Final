<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Safely add indexes - skip if they already exist
        try {
            Schema::table('member_networks', function ($table) {
                $table->index('path');
                $table->index('generation');
                $table->index('rank');
                $table->index(['parent_id', 'position']);
                $table->index('group');
            });
        } catch (Throwable $e) {
            // Indexes may already exist - skip
        }

        try {
            Schema::table('member_profile', function ($table) {
                $table->index(['country_id', 'province_id', 'city_id']);
                $table->index('phone');
            });
        } catch (Throwable $e) {
            // Indexes may already exist - skip
        }

        try {
            Schema::table('model_has_roles', function ($table) {
                $table->index(['model_type', 'model_id']);
            });
        } catch (Throwable $e) {
            // Indexes may already exist - skip
        }

        try {
            Schema::table('model_has_permissions', function ($table) {
                $table->index(['model_type', 'model_id']);
            });
        } catch (Throwable $e) {
            // Indexes may already exist - skip
        }
    }

    public function down(): void
    {
        try {
            Schema::table('member_networks', function ($table) {
                $table->dropIndex(['path']);
                $table->dropIndex(['generation']);
                $table->dropIndex(['rank']);
                $table->dropIndex(['parent_id', 'position']);
                $table->dropIndex(['group']);
            });
        } catch (Throwable $e) {
            // Ignore if index doesn't exist
        }

        try {
            Schema::table('member_profile', function ($table) {
                $table->dropIndex(['country_id', 'province_id', 'city_id']);
                $table->dropIndex(['phone']);
            });
        } catch (Throwable $e) {
            // Ignore if index doesn't exist
        }

        try {
            Schema::table('model_has_roles', function ($table) {
                $table->dropIndex(['model_type', 'model_id']);
            });
        } catch (Throwable $e) {
            // Ignore if index doesn't exist
        }

        try {
            Schema::table('model_has_permissions', function ($table) {
                $table->dropIndex(['model_type', 'model_id']);
            });
        } catch (Throwable $e) {
            // Ignore if index doesn't exist
        }
    }
};
