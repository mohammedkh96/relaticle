<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\DataSource;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DataSource>
 */
final class DataSourceFactory extends Factory
{
    protected $model = DataSource::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Website',
                'Exhibition',
                'Referral',
                'Cold Call',
                'Trade Show',
                'LinkedIn',
                'Google Ads',
                'Partner',
                'Import',
            ]) . ' ' . $this->faker->randomNumber(3),
            'description' => $this->faker->optional()->sentence(),
            'is_active' => $this->faker->boolean(90), // 90% chance of being active
        ];
    }

    public function active(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_active' => false,
        ]);
    }
}
