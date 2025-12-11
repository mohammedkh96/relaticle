<?php

declare(strict_types=1);

use App\Models\Event;
use App\Models\Company;
use App\Models\Participation;
use App\Models\Invoice;
use App\Models\Team;
use App\Enums\InvoiceStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Invoice Model', function () {
    test('invoice belongs to event', function () {
        $event = Event::factory()->create();
        $team = Team::factory()->create();
        $company = Company::factory()->for($team)->create();
        $participation = Participation::factory()->create([
            'event_id' => $event->id,
            'company_id' => $company->id,
        ]);

        $invoice = Invoice::factory()->create([
            'event_id' => $event->id,
            'company_id' => $company->id,
            'participation_id' => $participation->id,
        ]);

        expect($invoice->event)->toBeInstanceOf(Event::class);
        expect($invoice->event->id)->toBe($event->id);
    });

    test('invoice belongs to company', function () {
        $event = Event::factory()->create();
        $team = Team::factory()->create();
        $company = Company::factory()->for($team)->create();
        $participation = Participation::factory()->create([
            'event_id' => $event->id,
            'company_id' => $company->id,
        ]);

        $invoice = Invoice::factory()->create([
            'event_id' => $event->id,
            'company_id' => $company->id,
            'participation_id' => $participation->id,
        ]);

        expect($invoice->company)->toBeInstanceOf(Company::class);
        expect($invoice->company->id)->toBe($company->id);
    });

    test('invoice belongs to participation', function () {
        $event = Event::factory()->create();
        $team = Team::factory()->create();
        $company = Company::factory()->for($team)->create();
        $participation = Participation::factory()->create([
            'event_id' => $event->id,
            'company_id' => $company->id,
        ]);

        $invoice = Invoice::factory()->create([
            'event_id' => $event->id,
            'company_id' => $company->id,
            'participation_id' => $participation->id,
        ]);

        expect($invoice->participation)->toBeInstanceOf(Participation::class);
        expect($invoice->participation->id)->toBe($participation->id);
    });

    test('invoice has fillable attributes', function () {
        $invoice = Invoice::factory()->create([
            'invoice_number' => 'IN-001',
            'total_amount' => 5000.00,
            'notes' => 'Test invoice notes',
        ]);

        expect($invoice->invoice_number)->toBe('IN-001');
        expect((float) $invoice->total_amount)->toBe(5000.00);
        expect($invoice->notes)->toBe('Test invoice notes');
    });

    test('invoice casts dates correctly', function () {
        $invoice = Invoice::factory()->create([
            'issue_date' => '2025-01-15',
            'due_date' => '2025-01-30',
        ]);

        expect($invoice->issue_date)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
        expect($invoice->due_date)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });

    test('invoice casts status as enum', function () {
        $invoice = Invoice::factory()->create([
            'status' => InvoiceStatus::DRAFT,
        ]);

        expect($invoice->status)->toBeInstanceOf(InvoiceStatus::class);
        expect($invoice->status)->toBe(InvoiceStatus::DRAFT);
    });

    test('invoice casts items as array', function () {
        $items = [
            ['description' => 'Booth rental', 'amount' => 5000],
            ['description' => 'Equipment', 'amount' => 1000],
        ];

        $invoice = Invoice::factory()->create([
            'items' => $items,
        ]);

        expect($invoice->items)->toBeArray();
        expect($invoice->items)->toHaveCount(2);
    });

    test('invoice uses soft deletes', function () {
        $invoice = Invoice::factory()->create();

        $invoice->delete();

        expect($invoice->trashed())->toBeTrue();
        expect(Invoice::withTrashed()->find($invoice->id))->not->toBeNull();
    });
});
