<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Event;
use App\Models\Participation;
use App\Models\Team;
use Illuminate\Database\Seeder;

class ParticipationSeeder extends Seeder
{
    public function run(): void
    {
        $events = Event::orderBy('year')->get();

        if ($events->isEmpty()) {
            $this->command->warn('No events found. Run EventSeeder first.');
            return;
        }

        // Get or create a default team
        $team = Team::first();
        if (!$team) {
            $this->command->warn('No team found. Please create a team first.');
            return;
        }

        // Sample companies that attend regularly
        $loyalCompanies = [
            ['name' => 'Empire World Co', 'phone' => '+971501234567', 'address' => 'Downtown Dubai', 'country' => 'UAE', 'city' => 'Dubai'],
            ['name' => 'Global Investments LLC', 'phone' => '+971502345678', 'address' => 'ADGM', 'country' => 'UAE', 'city' => 'Abu Dhabi'],
            ['name' => 'Prime Properties Group', 'phone' => '+971503456789', 'address' => 'Marina', 'country' => 'UAE', 'city' => 'Dubai'],
            ['name' => 'Sunrise Capital', 'phone' => '+971504567890', 'address' => 'Al Majaz', 'country' => 'UAE', 'city' => 'Sharjah'],
            ['name' => 'Horizon Development', 'phone' => '+971505678901', 'address' => 'JLT', 'country' => 'UAE', 'city' => 'Dubai'],
        ];

        // Companies that attend occasionally
        $occasionalCompanies = [
            ['name' => 'Oval Investment Co', 'phone' => '+971506789012', 'address' => 'DIFC', 'country' => 'UAE', 'city' => 'Dubai'],
            ['name' => 'Metro Finance', 'phone' => '+971507890123', 'address' => 'Business Bay', 'country' => 'UAE', 'city' => 'Dubai'],
            ['name' => 'Atlas Holdings', 'phone' => '+966508901234', 'address' => 'Riyadh Financial District', 'country' => 'KSA', 'city' => 'Riyadh'],
            ['name' => 'Crown Real Estate', 'phone' => '+965509012345', 'address' => 'Kuwait Towers', 'country' => 'Kuwait', 'city' => 'Kuwait City'],
            ['name' => 'Vista Ventures', 'phone' => '+971510123456', 'address' => 'City Center', 'country' => 'UAE', 'city' => 'Ajman'],
            ['name' => 'Golden Gate Properties', 'phone' => '+973511234567', 'address' => 'Manama Centre', 'country' => 'Bahrain', 'city' => 'Manama'],
            ['name' => 'Summit Capital Group', 'phone' => '+974512345678', 'address' => 'West Bay', 'country' => 'Qatar', 'city' => 'Doha'],
            ['name' => 'Diamond Investments', 'phone' => '+968513456789', 'address' => 'Muscat Hills', 'country' => 'Oman', 'city' => 'Muscat'],
        ];

        $createdCount = 0;

        // Create loyal companies (attend all years)
        foreach ($loyalCompanies as $companyData) {
            $company = Company::updateOrCreate(
                ['name' => $companyData['name']],
                array_merge($companyData, ['team_id' => $team->id])
            );

            foreach ($events as $event) {
                Participation::updateOrCreate(
                    ['company_id' => $company->id, 'event_id' => $event->id],
                    ['stand_number' => 'A' . rand(1, 50)]
                );
                $createdCount++;
            }
        }

        // Create occasional companies (attend some years)
        foreach ($occasionalCompanies as $index => $companyData) {
            $company = Company::updateOrCreate(
                ['name' => $companyData['name']],
                array_merge($companyData, ['team_id' => $team->id])
            );

            // Each occasional company attends different years
            $yearsToAttend = $events->random(rand(2, 5));

            foreach ($yearsToAttend as $event) {
                Participation::updateOrCreate(
                    ['company_id' => $company->id, 'event_id' => $event->id],
                    ['stand_number' => chr(65 + ($index % 3)) . rand(1, 30)]
                );
                $createdCount++;
            }
        }

        $this->command->info("✅ Created {$createdCount} participations across " . $events->count() . " events");
    }
}
