<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        $events = [
            ['name' => 'Invest Expo', 'year' => 2019, 'start_date' => '2019-03-15', 'end_date' => '2019-03-18'],
            ['name' => 'Invest Expo', 'year' => 2020, 'start_date' => '2020-03-12', 'end_date' => '2020-03-15'],
            ['name' => 'Invest Expo', 'year' => 2021, 'start_date' => '2021-03-18', 'end_date' => '2021-03-21'],
            ['name' => 'Invest Expo', 'year' => 2022, 'start_date' => '2022-03-17', 'end_date' => '2022-03-20'],
            ['name' => 'Invest Expo', 'year' => 2023, 'start_date' => '2023-03-16', 'end_date' => '2023-03-19'],
            ['name' => 'Invest Expo', 'year' => 2024, 'start_date' => '2024-03-14', 'end_date' => '2024-03-17'],
            ['name' => 'Invest Expo', 'year' => 2025, 'start_date' => '2025-03-13', 'end_date' => '2025-03-16'],
            ['name' => 'Invest Expo', 'year' => 2026, 'start_date' => '2026-03-12', 'end_date' => '2026-03-15'],
        ];

        foreach ($events as $event) {
            Event::updateOrCreate(
                ['year' => $event['year'], 'name' => $event['name']],
                $event
            );
        }

        $this->command->info('✅ Created ' . count($events) . ' Invest Expo events (2019-2026)');
    }
}
