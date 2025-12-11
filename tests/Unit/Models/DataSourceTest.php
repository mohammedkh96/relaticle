<?php

declare(strict_types=1);

use App\Models\DataSource;
use App\Models\Company;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('DataSource Model', function () {
    test('data source has many companies', function () {
        $team = Team::factory()->create();
        $dataSource = DataSource::create([
            'name' => 'Website',
            'is_active' => true,
        ]);

        Company::factory()->count(3)->for($team)->create([
            'data_source_id' => $dataSource->id,
        ]);

        expect($dataSource->companies)->toHaveCount(3);
    });

    test('data source has fillable attributes', function () {
        $dataSource = DataSource::create([
            'name' => 'Exhibition',
            'description' => 'Collected from exhibitions',
            'is_active' => true,
        ]);

        expect($dataSource->name)->toBe('Exhibition');
        expect($dataSource->description)->toBe('Collected from exhibitions');
        expect($dataSource->is_active)->toBeTrue();
    });

    test('data source casts is_active as boolean', function () {
        $dataSource = DataSource::create([
            'name' => 'Referral',
            'is_active' => 1,
        ]);

        expect($dataSource->is_active)->toBeTrue();
        expect($dataSource->is_active)->toBeBool();
    });

    test('data source can be inactive', function () {
        $dataSource = DataSource::create([
            'name' => 'Old Source',
            'is_active' => false,
        ]);

        expect($dataSource->is_active)->toBeFalse();
    });

    test('data source can have null description', function () {
        $dataSource = DataSource::create([
            'name' => 'Import',
        ]);

        expect($dataSource->description)->toBeNull();
    });
});
