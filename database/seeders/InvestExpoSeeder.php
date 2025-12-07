<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class InvestExpoSeeder extends Seeder
{
    /**
     * Run all Invest Expo related seeders
     * 
     * Usage: php artisan db:seed --class=InvestExpoSeeder
     */
    public function run(): void
    {
        $this->command->info('🚀 Seeding Invest Expo Demo Data...');
        $this->command->newLine();

        $this->call([
            EventSeeder::class,
            ParticipationSeeder::class,
            VisitorSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('✅ Invest Expo demo data seeded successfully!');
        $this->command->info('   - Events: 2019-2026');
        $this->command->info('   - Companies: Loyal + Occasional participants');
        $this->command->info('   - Visitors: 50-150 per event');
    }
}
