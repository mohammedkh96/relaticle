<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReimportDataSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->warn('⚠️  Truncating tables: visitors, participations, people, companies...');

        Schema::disableForeignKeyConstraints();

        DB::table('visitors')->truncate();
        DB::table('participations')->truncate();
        DB::table('people')->truncate();
        DB::table('companies')->truncate();

        Schema::enableForeignKeyConstraints();

        $this->command->info('✅ Tables truncated.');
        $this->command->newLine();

        $this->command->info('🚀 Starting ImportLegacyDataSeeder...');
        $this->call(ImportLegacyDataSeeder::class);
    }
}
