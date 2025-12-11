<?php

declare(strict_types=1);

use App\Models\Event;
use App\Models\Company;
use App\Models\Participation;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Team;
use Relaticle\SystemAdmin\Models\SystemAdministrator;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Participation Model', function () {
    test('participation belongs to event', function () {
        $event = Event::factory()->create();
        $team = Team::factory()->create();
        $company = Company::factory()->for($team)->create();

        $participation = Participation::factory()->create([
            'event_id' => $event->id,
            'company_id' => $company->id,
        ]);

        expect($participation->event)->toBeInstanceOf(Event::class);
        expect($participation->event->id)->toBe($event->id);
    });

    test('participation belongs to company', function () {
        $event = Event::factory()->create();
        $team = Team::factory()->create();
        $company = Company::factory()->for($team)->create();

        $participation = Participation::factory()->create([
            'event_id' => $event->id,
            'company_id' => $company->id,
        ]);

        expect($participation->company)->toBeInstanceOf(Company::class);
        expect($participation->company->id)->toBe($company->id);
    });

    test('participation has many payments', function () {
        $event = Event::factory()->create();
        $team = Team::factory()->create();
        $company = Company::factory()->for($team)->create();

        $participation = Participation::factory()->create([
            'event_id' => $event->id,
            'company_id' => $company->id,
        ]);

        Payment::factory()->count(2)->create([
            'event_id' => $event->id,
            'participation_id' => $participation->id,
        ]);

        expect($participation->payments)->toHaveCount(2);
    });

    test('participation has many invoices', function () {
        $event = Event::factory()->create();
        $team = Team::factory()->create();
        $company = Company::factory()->for($team)->create();

        $participation = Participation::factory()->create([
            'event_id' => $event->id,
            'company_id' => $company->id,
        ]);

        Invoice::factory()->count(2)->create([
            'event_id' => $event->id,
            'company_id' => $company->id,
            'participation_id' => $participation->id,
        ]);

        expect($participation->invoices)->toHaveCount(2);
    });

    test('participation has fillable attributes', function () {
        $participation = Participation::factory()->create([
            'stand_number' => 'B-200',
            'booth_size' => '3x3',
            'booth_price' => 5000.00,
            'discount' => 500.00,
            'final_price' => 4500.00,
            'notes' => 'VIP Exhibitor',
        ]);

        expect($participation->stand_number)->toBe('B-200');
        expect($participation->booth_size)->toBe('3x3');
        expect((float) $participation->booth_price)->toBe(5000.00);
        expect((float) $participation->discount)->toBe(500.00);
        expect((float) $participation->final_price)->toBe(4500.00);
        expect($participation->notes)->toBe('VIP Exhibitor');
    });

    test('participation casts booleans correctly', function () {
        $participation = Participation::factory()->create([
            'logo_received' => true,
            'catalog_received' => false,
            'badge_names_received' => true,
        ]);

        expect($participation->logo_received)->toBeTrue();
        expect($participation->catalog_received)->toBeFalse();
        expect($participation->badge_names_received)->toBeTrue();
    });

    test('participation casts status as enum', function () {
        $participation = Participation::factory()->create();

        expect($participation->participation_status)->toBeInstanceOf(\App\Enums\ParticipationStatus::class);
    });

    test('participation casts decimal values correctly', function () {
        $participation = Participation::factory()->create([
            'booth_price' => 1500.50,
        ]);

        expect($participation->booth_price)->toBe('1500.50');
    });
});
