<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Event;
use App\Models\Visitor;
use Illuminate\Database\Eloquent\Factories\Factory;

class VisitorFactory extends Factory
{
    protected $model = Visitor::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'job' => $this->faker->jobTitle(),
            'country' => $this->faker->country(),
            'city' => $this->faker->city(),
            'event_id' => Event::factory(),
        ];
    }

    /**
     * Assign to a specific event
     */
    public function forEvent(Event $event): static
    {
        return $this->state(fn() => [
            'event_id' => $event->id,
        ]);
    }
}
