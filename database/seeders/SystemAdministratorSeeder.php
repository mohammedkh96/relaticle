<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Relaticle\SystemAdmin\Enums\SystemAdministratorRole;
use Relaticle\SystemAdmin\Models\SystemAdministrator;

final class SystemAdministratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Super Administrator - full access
        SystemAdministrator::firstOrCreate(
            ['email' => 'sysadmin@relaticle.com'],
            [
                'name' => 'System Administrator',
                'password' => bcrypt('password'),
                'role' => SystemAdministratorRole::SuperAdministrator,
                'email_verified_at' => now(),
            ]
        );

        // Administrator - can create/edit but not delete
        SystemAdministrator::firstOrCreate(
            ['email' => 'admin@investexpo.com'],
            [
                'name' => 'Admin',
                'password' => bcrypt('password'),
                'role' => SystemAdministratorRole::Administrator,
                'email_verified_at' => now(),
            ]
        );

        // Viewer - read-only access
        SystemAdministrator::firstOrCreate(
            ['email' => 'viewer@investexpo.com'],
            [
                'name' => 'Viewer',
                'password' => bcrypt('password'),
                'role' => SystemAdministratorRole::Viewer,
                'email_verified_at' => now(),
            ]
        );
    }
}
