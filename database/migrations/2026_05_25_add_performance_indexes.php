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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('member_networks', function ($table) {
            $table->dropIndexIfExists(['path']);
            $table->dropIndexIfExists(['generation']);
            $table->dropIndexIfExists(['rank']);
            $table->dropIndexIfExists(['parent_id', 'position']);
            $table->dropIndexIfExists(['group']);
        });

        Schema::table('member_profile', function ($table) {
            $table->dropIndexIfExists(['country_id', 'province_id', 'city_id']);
            $table->dropIndexIfExists(['phone']);
        });

        Schema::table('model_has_roles', function ($table) {
            $table->dropIndexIfExists(['model_type', 'model_id']);
        });

        Schema::table('model_has_permissions', function ($table) {
            $table->dropIndexIfExists(['model_type', 'model_id']);
        });
    }
};
