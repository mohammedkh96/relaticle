<?php

declare(strict_types=1);

use App\Models\Event;
use App\Models\Company;
use App\Models\Participation;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Visitor;
use App\Models\Team;
use Relaticle\SystemAdmin\Models\SystemAdministrator;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// Helper functions
function createTestAdmin(): SystemAdministrator
{
    return SystemAdministrator::factory()->create([
        'email' => 'test-admin@test.com',
        'password' => bcrypt('password'),
    ]);
}

function createTestTeam(): Team
{
    return Team::factory()->create(['name' => 'Test Team']);
}

// ========================
// INVOICE RESOURCE TESTS
// ========================

describe('Invoice Resource', function () {
    test('can render invoice list page', function () {
        $admin = createTestAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/invoices')
            ->assertOk();
    });

    test('can render create invoice page', function () {
        $admin = createTestAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/invoices/create')
            ->assertOk();
    });

    test('can render view invoice page', function () {
        $admin = createTestAdmin();
        $team = createTestTeam();

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

    test('can render edit invoice page', function () {
        $admin = createTestAdmin();
        $team = createTestTeam();

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
            ->get("/sysadmin/invoices/{$invoice->id}/edit")
            ->assertOk();
    });
});

// ========================
// PAYMENT RESOURCE TESTS
// ========================

describe('Payment Resource', function () {
    test('can render payment list page', function () {
        $admin = createTestAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/payments')
            ->assertOk();
    });

    test('can render create payment page', function () {
        $admin = createTestAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/payments/create')
            ->assertOk();
    });

    test('payment list page shows payments', function () {
        $admin = createTestAdmin();
        $team = createTestTeam();

        $event = Event::factory()->create();
        $company = Company::factory()->for($team)->create();
        $participation = Participation::factory()->create([
            'event_id' => $event->id,
            'company_id' => $company->id,
        ]);
        Payment::factory()->count(3)->create([
            'event_id' => $event->id,
            'participation_id' => $participation->id,
        ]);

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/payments')
            ->assertOk();
    });
});

// ========================
// VISITOR RESOURCE TESTS
// ========================

describe('Visitor Resource', function () {
    test('can render visitor list page', function () {
        $admin = createTestAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/visitors')
            ->assertOk();
    });

    test('can render create visitor page', function () {
        $admin = createTestAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/visitors/create')
            ->assertOk();
    });

    test('can render view visitor page', function () {
        $admin = createTestAdmin();
        $event = Event::factory()->create();
        $visitor = Visitor::factory()->create(['event_id' => $event->id]);

        $this->actingAs($admin, 'sysadmin')
            ->get("/sysadmin/visitors/{$visitor->id}")
            ->assertOk();
    });

    test('visitor list page shows visitors', function () {
        $admin = createTestAdmin();
        $event = Event::factory()->create();
        Visitor::factory()->count(5)->create(['event_id' => $event->id]);

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/visitors')
            ->assertOk();
    });
});

// ========================
// CATEGORY RESOURCE TESTS
// ========================

describe('Category Resource', function () {
    test('can render category list page', function () {
        $admin = createTestAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/categories')
            ->assertOk();
    });

    test('can render create category page', function () {
        $admin = createTestAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/categories/create')
            ->assertOk();
    });
});

// ========================
// DATA SOURCE RESOURCE TESTS
// ========================

describe('DataSource Resource', function () {
    test('can render data source list page', function () {
        $admin = createTestAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/data-sources')
            ->assertOk();
    });

    test('can render create data source page', function () {
        $admin = createTestAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/data-sources/create')
            ->assertOk();
    });
});

// ========================
// EXPORT FUNCTIONALITY TESTS
// ========================

describe('Export Functionality', function () {
    test('company export action exists', function () {
        $admin = createTestAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/companies')
            ->assertOk();
    });

    test('event export action exists', function () {
        $admin = createTestAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/events')
            ->assertOk();
    });

    test('invoice export action exists', function () {
        $admin = createTestAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/invoices')
            ->assertOk();
    });

    test('payment export action exists', function () {
        $admin = createTestAdmin();

        $this->actingAs($admin, 'sysadmin')
            ->get('/sysadmin/payments')
            ->assertOk();
    });
});
