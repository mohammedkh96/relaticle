<?php

declare(strict_types=1);

use App\Models\Event;
use App\Models\Company;
use App\Models\Participation;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Visitor;
use App\Models\Team;
use App\Models\User;
use Relaticle\SystemAdmin\Models\SystemAdministrator;
use Relaticle\SystemAdmin\Enums\SystemAdministratorRole;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

// ========================
// HELPER FUNCTIONS
// ========================

function createSuperAdmin(): SystemAdministrator
{
    return SystemAdministrator::factory()->create([
        'email' => 'superadmin@test.com',
        'password' => bcrypt('password'),
        'role' => SystemAdministratorRole::SuperAdministrator,
    ]);
}

function createAdmin(): SystemAdministrator
{
    return SystemAdministrator::factory()->create([
        'email' => 'admin@test.com',
        'password' => bcrypt('password'),
    ]);
}

function createTeam(): Team
{
    return Team::factory()->create(['name' => 'Test Team']);
}

function createUser(Team $team): User
{
    $user = User::factory()->create([
        'email' => 'user@test.com',
        'password' => bcrypt('password'),
        'current_team_id' => $team->id,
    ]);
    $user->teams()->attach($team->id, ['role' => 'member']);
    return $user;
}

// ========================
// PERFORMANCE TESTS
// ========================

describe('Performance', function () {
    test('dashboard loads within acceptable time', function () {
        $admin = createSuperAdmin();

        $start = microtime(true);
        $response = $this->actingAs($admin, 'sysadmin')->get('/sysadmin');
        $duration = microtime(true) - $start;

        $response->assertOk();
        expect($duration)->toBeLessThan(3.0); // Should load in under 3 seconds
    });

    test('events list handles large dataset', function () {
        $admin = createSuperAdmin();
        Event::factory()->count(100)->create();

        $start = microtime(true);
        $response = $this->actingAs($admin, 'sysadmin')->get('/sysadmin/events');
        $duration = microtime(true) - $start;

        $response->assertOk();
        expect($duration)->toBeLessThan(5.0); // Should handle 100 events in under 5 seconds
    });

    test('companies list handles large dataset', function () {
        $admin = createSuperAdmin();
        $team = createTeam();
        Company::factory()->count(100)->for($team)->create();

        $start = microtime(true);
        $response = $this->actingAs($admin, 'sysadmin')->get('/sysadmin/companies');
        $duration = microtime(true) - $start;

        $response->assertOk();
        expect($duration)->toBeLessThan(5.0);
    });

    test('database queries are optimized for events', function () {
        $admin = createSuperAdmin();
        Event::factory()->count(20)->create();

        DB::enableQueryLog();
        $this->actingAs($admin, 'sysadmin')->get('/sysadmin/events');
        $queryCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        // Should not have N+1 queries (reasonable limit)
        expect($queryCount)->toBeLessThan(30);
    });
});

// ========================
// LOGO AND ASSETS TESTS
// ========================

describe('Logo and Assets', function () {
    test('logo file exists in public directory', function () {
        expect(file_exists(public_path('images/logo.webp')))->toBeTrue();
    });

    test('logo is configured in panel provider', function () {
        $admin = createSuperAdmin();
        $response = $this->actingAs($admin, 'sysadmin')->get('/sysadmin');

        $response->assertOk();
        // Logo should be in the response HTML
        $response->assertSee('logo.webp', false);
    });
});

// ========================
// FULL CRUD TESTS - EVENTS
// ========================

describe('Event CRUD', function () {
    test('can list events', function () {
        $admin = createSuperAdmin();
        Event::factory()->count(5)->create();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/events')
            ->assertOk();
    });

    test('can view create event form', function () {
        $admin = createSuperAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/events/create')
            ->assertOk();
    });

    test('can view event details', function () {
        $admin = createSuperAdmin();
        $event = Event::factory()->create();

        $this->actingAs($admin, 'sysadmin')
            ->get("/sysadmin/events/{$event->id}")
            ->assertOk();
    });

    test('can view edit event form', function () {
        $admin = createSuperAdmin();
        $event = Event::factory()->create();

        $this->actingAs($admin, 'sysadmin')
            ->get("/sysadmin/events/{$event->id}/edit")
            ->assertOk();
    });
});

// ========================
// FULL CRUD TESTS - COMPANIES
// ========================

describe('Company CRUD', function () {
    test('can list companies', function () {
        $admin = createSuperAdmin();
        $team = createTeam();
        Company::factory()->count(5)->for($team)->create();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/companies')
            ->assertOk();
    });

    test('can view create company form', function () {
        $admin = createSuperAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/companies/create')
            ->assertOk();
    });

    test('can view company details', function () {
        $admin = createSuperAdmin();
        $team = createTeam();
        $company = Company::factory()->for($team)->create();

        $this->actingAs($admin, 'sysadmin')
            ->get("/sysadmin/companies/{$company->id}")
            ->assertOk();
    });

    test('can view edit company form', function () {
        $admin = createSuperAdmin();
        $team = createTeam();
        $company = Company::factory()->for($team)->create();

        $this->actingAs($admin, 'sysadmin')
            ->get("/sysadmin/companies/{$company->id}/edit")
            ->assertOk();
    });
});

// ========================
// FULL CRUD TESTS - INVOICES
// ========================

describe('Invoice CRUD', function () {
    test('can list invoices', function () {
        $admin = createSuperAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/invoices')
            ->assertOk();
    });

    test('can view create invoice form', function () {
        $admin = createSuperAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/invoices/create')
            ->assertOk();
    });

    test('can view invoice details', function () {
        $admin = createSuperAdmin();
        $team = createTeam();
        $event = Event::factory()->create();
        $company = Company::factory()->for($team)->create();
        $participation = Participation::factory()->create([
            'event_id' => $event->id,
            'company_id' => $company->id,
        ]);
        $invoice = Invoice::factory()->create([
            'event_id' => $event->id,
            'company_id' => $company->id,
            'participation_id' => $participation->id,
        ]);

        $this->actingAs($admin, 'sysadmin')
            ->get("/sysadmin/invoices/{$invoice->id}")
            ->assertOk();
    });
});

// ========================
// FULL CRUD TESTS - PAYMENTS
// ========================

describe('Payment CRUD', function () {
    test('can list payments', function () {
        $admin = createSuperAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/payments')
            ->assertOk();
    });

    test('can view create payment form', function () {
        $admin = createSuperAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/payments/create')
            ->assertOk();
    });
});

// ========================
// FULL CRUD TESTS - VISITORS
// ========================

describe('Visitor CRUD', function () {
    test('can list visitors', function () {
        $admin = createSuperAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/visitors')
            ->assertOk();
    });

    test('can view create visitor form', function () {
        $admin = createSuperAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/visitors/create')
            ->assertOk();
    });

    test('can view visitor details', function () {
        $admin = createSuperAdmin();
        $event = Event::factory()->create();
        $visitor = Visitor::factory()->create(['event_id' => $event->id]);

        $this->actingAs($admin, 'sysadmin')
            ->get("/sysadmin/visitors/{$visitor->id}")
            ->assertOk();
    });
});

// ========================
// FULL CRUD TESTS - OPPORTUNITIES
// ========================

describe('Opportunity CRUD', function () {
    test('can list opportunities', function () {
        $admin = createSuperAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/opportunities')
            ->assertOk();
    });

    test('can view create opportunity form', function () {
        $admin = createSuperAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/opportunities/create')
            ->assertOk();
    });
});

// ========================
// ROLE MANAGEMENT TESTS
// ========================

describe('Role Management - Super Administrator', function () {
    test('super admin can access all resources', function () {
        $admin = createSuperAdmin();

        $pages = [
            '/sysadmin/events',
            '/sysadmin/companies',
            '/sysadmin/invoices',
            '/sysadmin/payments',
            '/sysadmin/visitors',
            '/sysadmin/opportunities',
        ];

        foreach ($pages as $page) {
            $this->actingAs($admin, 'sysadmin')
                ->get($page)
                ->assertOk();
        }
    });

    test('super admin can access system administrator management', function () {
        $admin = createSuperAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/system-administrators')
            ->assertOk();
    });
});

describe('Role Management - Regular Admin', function () {
    test('regular admin can access most resources', function () {
        $admin = createAdmin();

        $pages = [
            '/sysadmin/events',
            '/sysadmin/companies',
            '/sysadmin/invoices',
        ];

        foreach ($pages as $page) {
            $this->actingAs($admin, 'sysadmin')
                ->get($page)
                ->assertOk();
        }
    });
});

// ========================
// AUTHENTICATION TESTS
// ========================

describe('Authentication', function () {
    test('unauthenticated access redirects to login', function () {
        $this->get('/sysadmin')
            ->assertRedirect('/sysadmin/login');
    });

    test('login page is accessible', function () {
        $this->get('/sysadmin/login')
            ->assertOk();
    });

    test('authenticated admin can access dashboard', function () {
        $admin = createSuperAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin')
            ->assertOk();
    });
});

// ========================
// SPECIAL PAGE TESTS
// ========================

describe('Special Pages', function () {
    test('exhibitors page loads', function () {
        $admin = createSuperAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/event-exhibitors')
            ->assertOk();
    });

    test('visitors page loads', function () {
        $admin = createSuperAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/event-visitors')
            ->assertOk();
    });
});
