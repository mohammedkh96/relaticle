<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Company;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Category Model', function () {
    test('category has many companies', function () {
        $team = Team::factory()->create();
        $category = Category::create([
            'name' => 'Construction',
            'is_active' => true,
        ]);

        Company::factory()->count(3)->for($team)->create([
            'category_id' => $category->id,
        ]);

        expect($category->companies)->toHaveCount(3);
    });

    test('category has fillable attributes', function () {
        $category = Category::create([
            'name' => 'Architecture',
            'description' => 'Architectural firms',
            'is_active' => true,
        ]);

        expect($category->name)->toBe('Architecture');
        expect($category->description)->toBe('Architectural firms');
        expect($category->is_active)->toBeTrue();
    });

    test('category casts is_active as boolean', function () {
        $category = Category::create([
            'name' => 'Real Estate',
            'is_active' => 1,
        ]);

        expect($category->is_active)->toBeTrue();
        expect($category->is_active)->toBeBool();
    });

    test('category can be inactive', function () {
        $category = Category::create([
            'name' => 'Inactive Category',
            'is_active' => false,
        ]);

        expect($category->is_active)->toBeFalse();
    });

    test('category can have null description', function () {
        $category = Category::create([
            'name' => 'Simple Category',
        ]);

        expect($category->description)->toBeNull();
    });
});
