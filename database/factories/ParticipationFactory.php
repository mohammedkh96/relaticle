<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Company;
use App\Models\Event;
use App\Models\Participation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ParticipationFactory extends Factory
{
    protected $model = Participation::class;

    public function definition(): array
    {
        return [
            'company_id' => Company::factory(),
            'event_id' => Event::factory(),
            'stand_number' => $this->faker->optional(0.7)->randomElement([
                'A' . $this->faker->numberBetween(1, 50),
                'B' . $this->faker->numberBetween(1, 50),
                'C' . $this->faker->numberBetween(1, 30),
            ]),
            'notes' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    /**
     * Assign to a specific event and company
     */
    public function forEvent(Event $event): static
    {
        return $this->state(fn() => [
            'event_id' => $event->id,
        ]);
    }

    public function forCompany(Company $company): static
    {
        return $this->state(fn() => [
            'company_id' => $company->id,
        ]);
    }
}
