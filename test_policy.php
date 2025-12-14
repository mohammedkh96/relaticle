<?php

use Illuminate\Support\Facades\Gate;
use App\Models\Visitor;
use App\Models\Participation;
use Relaticle\SystemAdmin\Models\SystemAdministrator;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

echo "Checking Policies...\n";

// Mock a SystemAdministrator
$user = SystemAdministrator::first();
if (!$user) {
    echo "No SystemAdministrator found in DB.\n";
    exit;
}

echo "User: " . $user->name . " (ID: " . $user->id . ")\n";
$app->make('auth')->guard('sysadmin')->setUser($user);

// Check Visitor Policy
$visitorPolicy = Gate::getPolicyFor(Visitor::class);
echo "Visitor Policy: " . ($visitorPolicy ? get_class($visitorPolicy) : 'NULL') . "\n";
echo "Can viewAny Visitor: " . (Gate::allows('viewAny', Visitor::class) ? 'YES' : 'NO') . "\n";

// Check Participation Policy
$participationPolicy = Gate::getPolicyFor(Participation::class);
echo "Participation Policy: " . ($participationPolicy ? get_class($participationPolicy) : 'NULL') . "\n";
echo "Can viewAny Participation: " . (Gate::allows('viewAny', Participation::class) ? 'YES' : 'NO') . "\n";

// Check permissions specifically if policy exists
if ($participationPolicy) {
    $policy = new $participationPolicy;
    if (method_exists($policy, 'viewAny')) {
        echo "Policy viewAny Result: " . ($policy->viewAny($user) ? 'TRUE' : 'FALSE') . "\n";
    }
}
