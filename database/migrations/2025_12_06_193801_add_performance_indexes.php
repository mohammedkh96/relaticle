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
        // Add indexes to improve query performance
        Schema::table('companies', function (Blueprint $table) {
            $table->index('account_owner_id');
        });

        Schema::table('people', function (Blueprint $table) {
            $table->index('company_id');
        });

        Schema::table('opportunities', function (Blueprint $table) {
            $table->index('company_id');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->index('created_by');
        });

        Schema::table('task_user', function (Blueprint $table) {
            $table->index('task_id');
            $table->index('user_id');
        });

        Schema::table('participations', function (Blueprint $table) {
            $table->index('event_id');
            $table->index('visitor_id');
            $table->index('company_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropIndex(['account_owner_id']);
        });

        Schema::table('people', function (Blueprint $table) {
            $table->dropIndex(['company_id']);
        });

        Schema::table('opportunities', function (Blueprint $table) {
            $table->dropIndex(['company_id']);
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['created_by']);
        });

        Schema::table('task_user', function (Blueprint $table) {
            $table->dropIndex(['task_id']);
            $table->dropIndex(['user_id']);
        });

        Schema::table('participations', function (Blueprint $table) {
            $table->dropIndex(['event_id']);
            $table->dropIndex(['visitor_id']);
            $table->dropIndex(['company_id']);
        });
    }
};
