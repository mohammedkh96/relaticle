<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Visitor;
use Illuminate\Database\Seeder;

class VisitorSeeder extends Seeder
{
    public function run(): void
    {
        $events = Event::orderBy('year')->get();

        if ($events->isEmpty()) {
            $this->command->warn('No events found. Run EventSeeder first.');
            return;
        }

        $faker = \Faker\Factory::create();
        $createdCount = 0;

        foreach ($events as $event) {
            // Each event has between 30-80 visitors
            $visitorCount = rand(30, 80);

            for ($i = 0; $i < $visitorCount; $i++) {
                Visitor::create([
                    'name' => $faker->name(),
                    'email' => $faker->unique()->safeEmail(),
                    'phone' => '+971' . $faker->numberBetween(501000000, 559999999),
                    'job' => $faker->randomElement([
                        'CEO',
                        'CFO',
                        'Director',
                        'Manager',
                        'Investment Analyst',
                        'Business Development',
                        'Marketing Manager',
                        'Sales Director',
                        'Real Estate Consultant',
                        'Financial Advisor',
                        'Project Manager'
                    ]),
                    'country' => $faker->randomElement(['UAE', 'KSA', 'Kuwait', 'Qatar', 'Bahrain', 'Oman']),
                    'city' => $faker->city(),
                    'event_id' => $event->id,
                ]);
                $createdCount++;
            }

            $this->command->info("  → {$event->year}: Created {$visitorCount} visitors");
        }

        $this->command->info("✅ Created {$createdCount} total visitors");
    }
}
