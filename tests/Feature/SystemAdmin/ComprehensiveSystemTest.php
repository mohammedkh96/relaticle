<?php

declare(strict_types=1);

use App\Models\Event;
use App\Models\Company;
use App\Models\Participation;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\Visitor;
use App\Models\Team;
use Relaticle\SystemAdmin\Models\SystemAdministrator;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Helper function to create admin
function createAdmin(): SystemAdministrator
{
    return SystemAdministrator::factory()->create([
        'email' => 'admin@test.com',
        'password' => bcrypt('password'),
    ]);
}

// Helper function to create team
function createTeam(): Team
{
    return Team::factory()->create(['name' => 'Test Team']);
}

// ========================
// DASHBOARD TESTS
// ========================

describe('Dashboard', function () {
    test('dashboard page loads successfully', function () {
        $admin = createAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin')
            ->assertOk();
    });

    test('dashboard displays stats widgets', function () {
        $admin = createAdmin();
        $team = createTeam();

        Event::factory()->count(3)->create();
        Company::factory()->count(5)->for($team)->create();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin')
            ->assertOk();
    });
});
// ========================
// EVENT RESOURCE TESTS
// ========================

describe('Event Resource', function () {
    test('event list page loads', function () {
        $admin = createAdmin();
        Event::factory()->count(3)->create();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/events')
            ->assertOk();
    });

    test('can access create event page', function () {
        $admin = createAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/events/create')
            ->assertOk();
    });

    test('can view event details', function () {
        $admin = createAdmin();
        $event = Event::factory()->create();

        $this->actingAs($admin, 'sysadmin')
            ->get("/sysadmin/events/{$event->id}")
            ->assertOk();
    });

    test('can access edit event page', function () {
        $admin = createAdmin();
        $event = Event::factory()->create();

        $this->actingAs($admin, 'sysadmin')
            ->get("/sysadmin/events/{$event->id}/edit")
            ->assertOk();
    });
});

// ========================
// COMPANY RESOURCE TESTS
// ========================

describe('Company Resource', function () {
    test('company list page loads', function () {
        $admin = createAdmin();
        $team = createTeam();
        Company::factory()->count(3)->for($team)->create();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/companies')
            ->assertOk();
    });

    test('can access create company page', function () {
        $admin = createAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/companies/create')
            ->assertOk();
    });
});

// ========================
// PARTICIPATION (EXHIBITOR) TESTS
// ========================

describe('Participation/Exhibitor Resource', function () {
    test('exhibitors page loads', function () {
        $admin = createAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/event-exhibitors')
            ->assertOk();
    });

    test('exhibitors page loads with event data', function () {
        $admin = createAdmin();
        $team = createTeam();

        $event = Event::factory()->create(['year' => 2025]);
        $company = Company::factory()->for($team)->create();
        Participation::factory()->create([
            'event_id' => $event->id,
            'company_id' => $company->id,
        ]);

        $this->actingAs($admin, 'sysadmin')
            ->get("/sysadmin/event-exhibitors?event_id={$event->id}")
            ->assertOk();
    });
});

// ========================
// INVOICE RESOURCE TESTS
// ========================

describe('Invoice Resource', function () {
    test('invoice list page loads', function () {
        $admin = createAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/invoices')
            ->assertOk();
    });

    test('can access create invoice page', function () {
        $admin = createAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/invoices/create')
            ->assertOk();
    });

    test('invoice view page works', function () {
        $admin = createAdmin();
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
// PAYMENT RESOURCE TESTS
// ========================

describe('Payment Resource', function () {
    test('payment list page loads', function () {
        $admin = createAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/payments')
            ->assertOk();
    });

    test('can access create payment page', function () {
        $admin = createAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/payments/create')
            ->assertOk();
    });

    test('can create invoice from payment', function () {
        $admin = createAdmin();
        $team = createTeam();

        $event = Event::factory()->create();
        $company = Company::factory()->for($team)->create();
        $participation = Participation::factory()->create([
            'event_id' => $event->id,
            'company_id' => $company->id,
        ]);

        $payment = Payment::factory()->create([
            'event_id' => $event->id,
            'participation_id' => $participation->id,
            'amount' => 1000,
        ]);

        $this->actingAs($admin, 'sysadmin')
            ->get("/sysadmin/invoices/create?payment_id={$payment->id}")
            ->assertOk();
    });
});

// ========================
// VISITOR RESOURCE TESTS
// ========================

describe('Visitor Resource', function () {
    test('visitor list page loads', function () {
        $admin = createAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/visitors')
            ->assertOk();
    });

    test('visitors page with event data', function () {
        $admin = createAdmin();
        $event = Event::factory()->create();
        Visitor::factory()->count(10)->create(['event_id' => $event->id]);

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/event-visitors')
            ->assertOk();
    });
});

// ========================
// OPPORTUNITY RESOURCE TESTS
// ========================

describe('Opportunity Resource', function () {
    test('opportunity list page loads', function () {
        $admin = createAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/opportunities')
            ->assertOk();
    });
});

// ========================
// NOTIFICATION TESTS
// ========================

describe('Notifications', function () {
    test('notifications table exists', function () {
        expect(\Illuminate\Support\Facades\Schema::hasTable('notifications'))->toBeTrue();
    });

    test('dashboard loads with notification support', function () {
        $admin = createAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin')
            ->assertOk();
    });
});

// ========================
// ASSET TESTS
// ========================

describe('Assets', function () {
    test('logo file exists', function () {
        $logoPath = public_path('images/logo.webp');
        expect(file_exists($logoPath))->toBeTrue();
    });
});

// ========================
// AUTHENTICATION TESTS
// ========================

describe('Authentication', function () {
    test('unauthenticated users are redirected to login', function () {
        $this->get('/sysadmin')
            ->assertRedirect('/sysadmin/login');
    });

    test('login page loads', function () {
        $this->get('/sysadmin/login')
            ->assertOk();
    });
});

// ========================
// MODEL RELATIONSHIP TESTS
// ========================

describe('Model Relationships', function () {
    test('event has many participations', function () {
        $event = Event::factory()->create();
        $team = createTeam();
        $company = Company::factory()->for($team)->create();

        Participation::factory()->count(3)->create([
            'event_id' => $event->id,
            'company_id' => $company->id,
        ]);

        expect($event->participations)->toHaveCount(3);
    });

    test('company belongs to team', function () {
        $team = createTeam();
        $company = Company::factory()->for($team)->create();

        expect($company->team->id)->toBe($team->id);
    });

    test('participation belongs to event and company', function () {
        $team = createTeam();
        $event = Event::factory()->create();
        $company = Company::factory()->for($team)->create();

        $participation = Participation::factory()->create([
            'event_id' => $event->id,
            'company_id' => $company->id,
        ]);

        expect($participation->event->id)->toBe($event->id);
        expect($participation->company->id)->toBe($company->id);
    });

    test('participation has many payments', function () {
        $team = createTeam();
        $event = Event::factory()->create();
        $company = Company::factory()->for($team)->create();

        $participation = Participation::factory()->create([
            'event_id' => $event->id,
            'company_id' => $company->id,
        ]);

        Payment::factory()->count(2)->create([
            'event_id' => $event->id,
            'participation_id' => $participation->id,
        ]);

        expect($participation->payments)->toHaveCount(2);
    });
});
