<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes to improve query performance - with safety checks

        if (Schema::hasColumn('companies', 'account_owner_id')) {
            Schema::table('companies', function (Blueprint $table) {
                $table->index('account_owner_id');
            });
        }

        if (Schema::hasColumn('people', 'company_id')) {
            Schema::table('people', function (Blueprint $table) {
                $table->index('company_id');
            });
        }

        if (Schema::hasColumn('opportunities', 'company_id')) {
            Schema::table('opportunities', function (Blueprint $table) {
                $table->index('company_id');
            });
        }

        if (Schema::hasColumn('participations', 'event_id')) {
            Schema::table('participations', function (Blueprint $table) {
                $table->index('event_id');
            });
        }

        if (Schema::hasColumn('participations', 'company_id')) {
            Schema::table('participations', function (Blueprint $table) {
                $table->index('company_id');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop indexes safely
        try {
            Schema::table('companies', function (Blueprint $table) {
                $table->dropIndex(['account_owner_id']);
            });
        } catch (\Exception $e) {
        }

        try {
            Schema::table('people', function (Blueprint $table) {
                $table->dropIndex(['company_id']);
            });
        } catch (\Exception $e) {
        }

        try {
            Schema::table('opportunities', function (Blueprint $table) {
                $table->dropIndex(['company_id']);
            });
        } catch (\Exception $e) {
        }

        try {
            Schema::table('participations', function (Blueprint $table) {
                $table->dropIndex(['event_id']);
                $table->dropIndex(['company_id']);
            });
        } catch (\Exception $e) {
        }
    }
};
