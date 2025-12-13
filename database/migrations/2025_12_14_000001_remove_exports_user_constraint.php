<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('exports', function (Blueprint $table) {
            // Drop the foreign key constraint
            // The default name is usually exports_user_id_foreign
            $table->dropForeign(['user_id']);

            // Optionally make it nullable if needed, but the issue is the constraint
            $table->foreignId('user_id')->change();
        });
    }

    public function down(): void
    {
        Schema::table('exports', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
        });
    }
};
