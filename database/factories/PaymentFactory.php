<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Event;
use App\Models\Payment;
use App\Models\Participation;
use App\Enums\PaymentStatus;
use App\Enums\PaymentType;
use App\Enums\PaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'participation_id' => Participation::factory(),
            'amount' => $this->faker->randomFloat(2, 100, 10000),
            'payment_date' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'status' => $this->faker->randomElement(PaymentStatus::cases()),
            'type' => $this->faker->randomElement(PaymentType::cases()),
            'method' => $this->faker->randomElement(PaymentMethod::cases()),
            'transaction_ref' => $this->faker->optional(0.7)->uuid(),
        ];
    }

    public function forEvent(Event $event): static
    {
        return $this->state(fn() => [
            'event_id' => $event->id,
        ]);
    }

    public function forParticipation(Participation $participation): static
    {
        return $this->state(fn() => [
            'participation_id' => $participation->id,
            'event_id' => $participation->event_id,
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn() => [
            'status' => PaymentStatus::COMPLETED,
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn() => [
            'status' => PaymentStatus::PENDING,
        ]);
    }
}
