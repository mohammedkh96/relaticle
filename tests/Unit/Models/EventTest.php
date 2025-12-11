<?php

declare(strict_types=1);

use App\Models\Event;
use App\Models\Company;
use App\Models\Participation;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Visitor;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Event Model', function () {
    test('event has many participations', function () {
        $event = Event::factory()->create();
        $team = Team::factory()->create();
        $company = Company::factory()->for($team)->create();

        Participation::factory()->count(3)->create([
            'event_id' => $event->id,
            'company_id' => $company->id,
        ]);

        expect($event->participations)->toHaveCount(3);
    });

    test('event has many visitors', function () {
        $event = Event::factory()->create();

        Visitor::factory()->count(5)->create([
            'event_id' => $event->id,
        ]);

        expect($event->visitors)->toHaveCount(5);
    });

    test('event has many invoices', function () {
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

        expect($event->invoices)->toHaveCount(2);
    });

    test('event has many payments', function () {
        $event = Event::factory()->create();
        $team = Team::factory()->create();
        $company = Company::factory()->for($team)->create();
        $participation = Participation::factory()->create([
            'event_id' => $event->id,
            'company_id' => $company->id,
        ]);

        Payment::factory()->count(3)->create([
            'event_id' => $event->id,
            'participation_id' => $participation->id,
        ]);

        expect($event->payments)->toHaveCount(3);
    });

    test('event belongs to many companies through participations', function () {
        $event = Event::factory()->create();
        $team = Team::factory()->create();
        $company = Company::factory()->for($team)->create();

        Participation::factory()->create([
            'event_id' => $event->id,
            'company_id' => $company->id,
            'stand_number' => 'A-100',
        ]);

        expect($event->companies->first()->id)->toBe($company->id);
        expect($event->companies->first()->pivot->stand_number)->toBe('A-100');
    });

    test('event has fillable attributes', function () {
        $event = Event::factory()->create([
            'name' => 'Test Event 2025',
            'year' => 2025,
            'location' => 'Convention Center',
        ]);

        expect($event->name)->toBe('Test Event 2025');
        expect($event->year)->toBe(2025);
        expect($event->location)->toBe('Convention Center');
    });

    test('event casts dates correctly', function () {
        $event = Event::factory()->create([
            'start_date' => '2025-06-01',
            'end_date' => '2025-06-05',
        ]);

        expect($event->start_date)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
        expect($event->end_date)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });

    test('event casts status as enum', function () {
        $event = Event::factory()->create();

        expect($event->status)->toBeInstanceOf(\App\Enums\EventStatus::class);
    });
});
