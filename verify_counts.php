<?php

use App\Models\Event;
use App\Models\Opportunity;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "--- Event Participation Counts ---\n";
foreach (Event::withCount('participations')->get() as $e) {
    echo "{$e->name}: {$e->participations_count}\n";
}

echo "\n--- Opportunities ---\n";
echo "Total Opportunities: " . Opportunity::count() . "\n";
