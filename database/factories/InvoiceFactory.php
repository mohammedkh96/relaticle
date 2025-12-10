<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Event;
use App\Models\Invoice;
use App\Models\Company;
use App\Models\Participation;
use App\Enums\InvoiceStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'participation_id' => Participation::factory(),
            'company_id' => Company::factory(),
            'invoice_number' => 'INV-' . $this->faker->unique()->numberBetween(1000, 9999),
            'issue_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'due_date' => $this->faker->optional(0.8)->dateTimeBetween('now', '+30 days'),
            'total_amount' => $this->faker->randomFloat(2, 500, 50000),
            'status' => $this->faker->randomElement(InvoiceStatus::cases()),
            'items' => [
                [
                    'description' => 'Exhibition Space Rental',
                    'quantity' => 1,
                    'unit_price' => $this->faker->randomFloat(2, 500, 50000),
                    'amount' => $this->faker->randomFloat(2, 500, 50000),
                ]
            ],
            'notes' => $this->faker->optional(0.3)->sentence(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn() => [
            'status' => InvoiceStatus::DRAFT,
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn() => [
            'status' => InvoiceStatus::SENT,
        ]);
    }

    public function paid(): static
    {
        return $this->state(fn() => [
            'status' => InvoiceStatus::PAID,
        ]);
    }
}
