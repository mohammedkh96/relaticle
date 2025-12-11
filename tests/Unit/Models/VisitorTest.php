<?php

declare(strict_types=1);

use App\Models\Event;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Visitor Model', function () {
    test('visitor belongs to event', function () {
        $event = Event::factory()->create();

        $visitor = Visitor::factory()->create([
            'event_id' => $event->id,
        ]);

        expect($visitor->event)->toBeInstanceOf(Event::class);
        expect($visitor->event->id)->toBe($event->id);
    });

    test('visitor has fillable attributes', function () {
        $event = Event::factory()->create();

        $visitor = Visitor::factory()->create([
            'event_id' => $event->id,
            'name' => 'John Doe',
            'phone' => '+1234567890',
            'email' => 'john@example.com',
            'job' => 'Engineer',
            'country' => 'USA',
            'city' => 'New York',
        ]);

        expect($visitor->name)->toBe('John Doe');
        expect($visitor->phone)->toBe('+1234567890');
        expect($visitor->email)->toBe('john@example.com');
        expect($visitor->job)->toBe('Engineer');
        expect($visitor->country)->toBe('USA');
        expect($visitor->city)->toBe('New York');
    });

    test('visitor can be created with factory', function () {
        $visitor = Visitor::factory()->create();

        expect($visitor)->toBeInstanceOf(Visitor::class);
        expect($visitor->id)->not->toBeNull();
    });

    test('multiple visitors can belong to same event', function () {
        $event = Event::factory()->create();

        Visitor::factory()->count(10)->create([
            'event_id' => $event->id,
        ]);

        expect(Visitor::where('event_id', $event->id)->count())->toBe(10);
    });
});
