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
        // 1. Update Events Table
        Schema::table('events', function (Blueprint $table) {
            $table->string('location')->nullable()->after('year');
            $table->string('status')->default('upcoming')->after('end_date'); // upcoming, running, finished
        });

        // 2. Update Participations Table
        Schema::table('participations', function (Blueprint $table) {
            $table->string('booth_size')->nullable()->after('notes');
            $table->decimal('booth_price', 15, 2)->default(0)->after('booth_size');
            $table->decimal('discount', 15, 2)->default(0)->after('booth_price');
            $table->decimal('final_price', 15, 2)->default(0)->after('discount');
            $table->string('participation_status')->default('reserved')->after('final_price'); // reserved, confirmed, cancelled

            // Document Confirmations
            $table->boolean('logo_received')->default(false)->after('participation_status');
            $table->boolean('catalog_received')->default(false)->after('logo_received');
            $table->boolean('badge_names_received')->default(false)->after('catalog_received');

            $table->timestamp('confirmed_at')->nullable()->after('badge_names_received');
            // assuming system_administrators table exists, but we use foreignId normally.
            // Using logic to allow null if deleted.
            if (Schema::hasTable('system_administrators')) {
                $table->foreignId('confirmed_by')->nullable()->constrained('system_administrators')->nullOnDelete()->after('confirmed_at');
            } else {
                $table->unsignedBigInteger('confirmed_by')->nullable()->after('confirmed_at');
            }
        });

        // 3. Create Payments Table
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('participation_id')->constrained()->cascadeOnDelete();

            $table->decimal('amount', 15, 2);
            $table->string('type')->default('deposit'); // deposit, final, additional
            $table->string('method')->default('bank_transfer'); // bank_transfer, cash, check, stripe
            $table->string('transaction_ref')->nullable();

            if (Schema::hasTable('system_administrators')) {
                $table->foreignId('received_by')->nullable()->constrained('system_administrators')->nullOnDelete();
            } else {
                $table->unsignedBigInteger('received_by')->nullable();
            }

            $table->date('payment_date');
            $table->string('status')->default('pending'); // pending, paid, rejected

            $table->timestamps();
            $table->softDeletes();
        });

        // 4. Create Invoices Table
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->foreignId('participation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->string('invoice_number')->unique();
            $table->date('issue_date');
            $table->date('due_date');

            $table->decimal('total_amount', 15, 2);
            $table->string('status')->default('draft'); // draft, sent, paid, overdue

            $table->json('items')->nullable(); // Snapshot of items

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('payments');

        Schema::table('participations', function (Blueprint $table) {
            $table->dropForeign(['confirmed_by']);
            $table->dropColumn([
                'booth_size',
                'booth_price',
                'discount',
                'final_price',
                'participation_status',
                'logo_received',
                'catalog_received',
                'badge_names_received',
                'confirmed_at',
                'confirmed_by'
            ]);
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['location', 'status']);
        });
    }
};
