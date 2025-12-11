<?php

declare(strict_types=1);

use App\Models\Event;
use App\Models\Company;
use App\Models\Participation;
use App\Models\Payment;
use App\Models\Team;
use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Payment Model', function () {
    test('payment belongs to event', function () {
        $event = Event::factory()->create();
        $team = Team::factory()->create();
        $company = Company::factory()->for($team)->create();
        $participation = Participation::factory()->create([
            'event_id' => $event->id,
            'company_id' => $company->id,
        ]);

        $payment = Payment::factory()->create([
            'event_id' => $event->id,
            'participation_id' => $participation->id,
        ]);

        expect($payment->event)->toBeInstanceOf(Event::class);
        expect($payment->event->id)->toBe($event->id);
    });

    test('payment belongs to participation', function () {
        $event = Event::factory()->create();
        $team = Team::factory()->create();
        $company = Company::factory()->for($team)->create();
        $participation = Participation::factory()->create([
            'event_id' => $event->id,
            'company_id' => $company->id,
        ]);

        $payment = Payment::factory()->create([
            'event_id' => $event->id,
            'participation_id' => $participation->id,
        ]);

        expect($payment->participation)->toBeInstanceOf(Participation::class);
        expect($payment->participation->id)->toBe($participation->id);
    });

    test('payment has fillable attributes', function () {
        $payment = Payment::factory()->create([
            'amount' => 2500.00,
            'transaction_ref' => 'TXN-123456',
        ]);

        expect((float) $payment->amount)->toBe(2500.00);
        expect($payment->transaction_ref)->toBe('TXN-123456');
    });

    test('payment casts type as enum', function () {
        $payment = Payment::factory()->create([
            'type' => PaymentType::DEPOSIT,
        ]);

        expect($payment->type)->toBeInstanceOf(PaymentType::class);
        expect($payment->type)->toBe(PaymentType::DEPOSIT);
    });

    test('payment casts method as enum', function () {
        $payment = Payment::factory()->create([
            'method' => PaymentMethod::BANK_TRANSFER,
        ]);

        expect($payment->method)->toBeInstanceOf(PaymentMethod::class);
        expect($payment->method)->toBe(PaymentMethod::BANK_TRANSFER);
    });

    test('payment casts status as enum', function () {
        $payment = Payment::factory()->create([
            'status' => PaymentStatus::PENDING,
        ]);

        expect($payment->status)->toBeInstanceOf(PaymentStatus::class);
        expect($payment->status)->toBe(PaymentStatus::PENDING);
    });

    test('payment casts payment_date correctly', function () {
        $payment = Payment::factory()->create([
            'payment_date' => '2025-01-15',
        ]);

        expect($payment->payment_date)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });

    test('payment uses soft deletes', function () {
        $payment = Payment::factory()->create();

        $payment->delete();

        expect($payment->trashed())->toBeTrue();
        expect(Payment::withTrashed()->find($payment->id))->not->toBeNull();
    });

    test('payment casts amount as decimal', function () {
        $payment = Payment::factory()->create([
            'amount' => 1234.56,
        ]);

        expect($payment->amount)->toBe('1234.56');
    });
});
