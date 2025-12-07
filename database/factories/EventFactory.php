<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;

class EventFactory extends Factory
{
    protected $model = Event::class;

    public function definition(): array
    {
        $year = $this->faker->numberBetween(2019, 2026);

        return [
            'name' => 'Invest Expo',
            'year' => $year,
            'start_date' => "{$year}-03-15",
            'end_date' => "{$year}-03-18",
        ];
    }

    /**
     * Create a specific year event
     */
    public function year(int $year): static
    {
        return $this->state(fn() => [
            'year' => $year,
            'start_date' => "{$year}-03-15",
            'end_date' => "{$year}-03-18",
        ]);
    }
}
