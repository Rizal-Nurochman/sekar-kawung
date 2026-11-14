<?php

use App\Support\ProductCatalog;

it('returns products with every field the views and modal use', function () {
    $products = ProductCatalog::all();

    expect($products)->not->toBeEmpty();

    foreach ($products as $product) {
        expect($product)->toHaveKeys([
            'id',
            'name',
            'category',
            'categoryName',
            'spec',
            'emission',
            'cert',
            'image',
            'desc',
            'company',
        ]);

        expect($product['company'])->toHaveKeys([
            'name',
            'location',
            'moq',
            'compliance',
            'energySource',
            'waterUsage',
            'recycledPct',
            'chemicalInfo',
            'processSteps',
        ]);

        expect($product['company']['processSteps'])->toBeArray();

        foreach ($product['company']['processSteps'] as $step) {
            expect($step)->toHaveKeys(['title', 'desc']);
        }

        expect(file_exists(public_path(ltrim($product['image'], '/'))))->toBeTrue();
    }
});

it('has unique product ids', function () {
    $ids = array_column(ProductCatalog::all(), 'id');

    expect(count(array_unique($ids)))->toBe(count($ids));
});

it('offers filter categories that match product categories', function () {
    $categoryIds = array_column(ProductCatalog::categories(), 'id');
    $productCategories = array_unique(array_column(ProductCatalog::all(), 'category'));

    expect($categoryIds)->toContain('all');
    expect(array_diff($productCategories, $categoryIds))->toBeEmpty();
});
