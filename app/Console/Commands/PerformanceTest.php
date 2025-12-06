<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use App\Models\Company;
use App\Models\People;
use App\Models\Event;
use App\Models\Visitor;
use App\Models\Participation;
use App\Models\Opportunity;
use App\Models\Task;

class PerformanceTest extends Command
{
    protected $signature = 'performance:test {--detailed : Show detailed query information}';
    protected $description = 'Test application performance and identify bottlenecks';

    public function handle()
    {
        $this->info('🔍 Starting Performance Test...');
        $this->newLine();

        // Test 1: Database Query Performance
        $this->testDatabaseQueries();

        // Test 2: Navigation Badge Performance
        $this->testNavigationBadges();

        // Test 3: Resource Loading Performance
        $this->testResourceLoading();

        // Test 4: Cache Performance
        $this->testCachePerformance();

        // Test 5: Eager Loading Verification
        $this->testEagerLoading();

        $this->newLine();
        $this->info('✅ Performance Test Complete!');
    }

    protected function testDatabaseQueries()
    {
        $this->info('📊 Test 1: Database Query Performance');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        DB::enableQueryLog();

        // Test simple queries
        $start = microtime(true);
        Company::count();
        $companyTime = (microtime(true) - $start) * 1000;

        $start = microtime(true);
        People::count();
        $peopleTime = (microtime(true) - $start) * 1000;

        $start = microtime(true);
        Event::count();
        $eventTime = (microtime(true) - $start) * 1000;

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $this->table(
            ['Model', 'Time (ms)', 'Status'],
            [
                ['Company', number_format($companyTime, 2), $this->getStatus($companyTime)],
                ['People', number_format($peopleTime, 2), $this->getStatus($peopleTime)],
                ['Event', number_format($eventTime, 2), $this->getStatus($eventTime)],
            ]
        );

        if ($this->option('detailed')) {
            $this->line('Total Queries: ' . count($queries));
        }

        $this->newLine();
    }

    protected function testNavigationBadges()
    {
        $this->info('🏷️  Test 2: Navigation Badge Performance');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $results = [];

        // Test with cache
        Cache::flush();
        $start = microtime(true);
        $count = Cache::remember('test_badge_company', 60, fn() => Company::count());
        $cachedTime = (microtime(true) - $start) * 1000;

        // Test without cache
        Cache::forget('test_badge_company');
        $start = microtime(true);
        $count = Company::count();
        $uncachedTime = (microtime(true) - $start) * 1000;

        $this->table(
            ['Type', 'Time (ms)', 'Improvement'],
            [
                ['Without Cache', number_format($uncachedTime, 2), '-'],
                ['With Cache', number_format($cachedTime, 2), number_format(($uncachedTime - $cachedTime) / $uncachedTime * 100, 1) . '%'],
            ]
        );

        $this->newLine();
    }

    protected function testResourceLoading()
    {
        $this->info('📦 Test 3: Resource Loading Performance');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        DB::enableQueryLog();

        // Test loading with relationships
        $start = microtime(true);
        $companies = Company::with(['accountOwner', 'people', 'opportunities'])->limit(10)->get();
        $withEagerTime = (microtime(true) - $start) * 1000;
        $withEagerQueries = count(DB::getQueryLog());

        DB::flushQueryLog();

        // Test loading without relationships
        $start = microtime(true);
        $companies = Company::limit(10)->get();
        foreach ($companies as $company) {
            $company->accountOwner;
            $company->people;
            $company->opportunities;
        }
        $withoutEagerTime = (microtime(true) - $start) * 1000;
        $withoutEagerQueries = count(DB::getQueryLog());

        DB::disableQueryLog();

        $this->table(
            ['Method', 'Time (ms)', 'Queries', 'Status'],
            [
                ['Without Eager Loading', number_format($withoutEagerTime, 2), $withoutEagerQueries, '❌ N+1 Problem'],
                ['With Eager Loading', number_format($withEagerTime, 2), $withEagerQueries, '✅ Optimized'],
            ]
        );

        $improvement = ($withoutEagerTime - $withEagerTime) / $withoutEagerTime * 100;
        $this->info("⚡ Performance Improvement: " . number_format($improvement, 1) . "%");

        $this->newLine();
    }

    protected function testCachePerformance()
    {
        $this->info('💾 Test 4: Cache Performance');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Test cache write
        $start = microtime(true);
        Cache::put('test_key', 'test_value', 60);
        $writeTime = (microtime(true) - $start) * 1000;

        // Test cache read
        $start = microtime(true);
        Cache::get('test_key');
        $readTime = (microtime(true) - $start) * 1000;

        // Test cache miss
        $start = microtime(true);
        Cache::get('non_existent_key');
        $missTime = (microtime(true) - $start) * 1000;

        $this->table(
            ['Operation', 'Time (ms)', 'Status'],
            [
                ['Cache Write', number_format($writeTime, 2), $this->getStatus($writeTime)],
                ['Cache Read (Hit)', number_format($readTime, 2), $this->getStatus($readTime)],
                ['Cache Read (Miss)', number_format($missTime, 2), $this->getStatus($missTime)],
            ]
        );

        Cache::forget('test_key');
        $this->newLine();
    }

    protected function testEagerLoading()
    {
        $this->info('🔗 Test 5: Eager Loading Verification');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        $tests = [
            'Companies' => fn() => Company::with(['accountOwner', 'people', 'opportunities'])->limit(5)->get(),
            'People' => fn() => People::with(['company'])->limit(5)->get(),
            'Opportunities' => fn() => Opportunity::with(['company'])->limit(5)->get(),
            'Tasks' => fn() => Task::with(['creator', 'assignees'])->limit(5)->get(),
            'Participations' => fn() => Participation::with(['event', 'visitor', 'company'])->limit(5)->get(),
        ];

        $results = [];

        foreach ($tests as $name => $query) {
            DB::enableQueryLog();
            $start = microtime(true);
            $query();
            $time = (microtime(true) - $start) * 1000;
            $queries = count(DB::getQueryLog());
            DB::disableQueryLog();

            $results[] = [
                $name,
                number_format($time, 2),
                $queries,
                $queries <= 6 ? '✅ Good' : '⚠️  Check',
            ];
        }

        $this->table(['Resource', 'Time (ms)', 'Queries', 'Status'], $results);

        $this->newLine();
    }

    protected function getStatus($time)
    {
        if ($time < 10)
            return '✅ Excellent';
        if ($time < 50)
            return '✅ Good';
        if ($time < 100)
            return '⚠️  Fair';
        return '❌ Slow';
    }
}
