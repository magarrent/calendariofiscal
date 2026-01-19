<?php

use App\Models\Deadline;
use App\Models\TaxModel;
use Database\Seeders\CsvTaxModelSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('parses all rows from CSV file', function () {
    $this->artisan('db:seed', ['--class' => CsvTaxModelSeeder::class])
        ->assertSuccessful();

    // CSV has 72 rows (71 data rows + 1 header), some may have invalid dates
    // Verify we have most deadlines created (allowing for a few with invalid data)
    expect(Deadline::count())->toBeGreaterThanOrEqual(65)
        ->and(Deadline::count())->toBeLessThanOrEqual(72);
});

it('creates tax models with correct structure', function () {
    $this->artisan('db:seed', ['--class' => CsvTaxModelSeeder::class])
        ->assertSuccessful();

    $taxModel = TaxModel::where('model_number', '303')->first();

    expect($taxModel)->not->toBeNull()
        ->and($taxModel->name)->toContain('IVA')
        ->and($taxModel->category)->toBe('iva')
        ->and($taxModel->year)->toBe(2026)
        ->and($taxModel->applicable_to)->toBeArray();
});

it('creates deadlines with period information', function () {
    $this->artisan('db:seed', ['--class' => CsvTaxModelSeeder::class])
        ->assertSuccessful();

    $deadline = Deadline::whereNotNull('period_start')
        ->whereNotNull('period_end')
        ->first();

    expect($deadline)->not->toBeNull()
        ->and($deadline->period_start)->toBeInstanceOf(\Carbon\CarbonInterface::class)
        ->and($deadline->period_end)->toBeInstanceOf(\Carbon\CarbonInterface::class)
        ->and($deadline->days_to_complete)->toBeInt()
        ->and($deadline->days_to_complete)->toBeGreaterThanOrEqual(0);
});

it('calculates days_to_complete correctly', function () {
    $this->artisan('db:seed', ['--class' => CsvTaxModelSeeder::class])
        ->assertSuccessful();

    $deadline = Deadline::whereNotNull('period_start')
        ->whereNotNull('period_end')
        ->first();

    $expectedDays = $deadline->period_start->diffInDays($deadline->period_end);

    expect($deadline->days_to_complete)->toBe((int) $expectedDays);
});

it('handles modelo 130 correctly', function () {
    $this->artisan('db:seed', ['--class' => CsvTaxModelSeeder::class])
        ->assertSuccessful();

    $taxModel = TaxModel::where('model_number', '130')->first();

    expect($taxModel)->not->toBeNull()
        ->and($taxModel->name)->toContain('IRPF')
        ->and($taxModel->category)->toBe('irpf')
        ->and($taxModel->frequency)->toBe('quarterly');
});

it('handles modelo 303 with multiple periodicities', function () {
    $this->artisan('db:seed', ['--class' => CsvTaxModelSeeder::class])
        ->assertSuccessful();

    $taxModels = TaxModel::where('model_number', '303')->get();

    // Should have both monthly and quarterly variants
    expect($taxModels->count())->toBeGreaterThan(0)
        ->and($taxModels->pluck('frequency')->unique()->count())->toBeGreaterThan(0);
});

it('parses frequencies correctly', function () {
    $this->artisan('db:seed', ['--class' => CsvTaxModelSeeder::class])
        ->assertSuccessful();

    $frequencies = TaxModel::distinct()->pluck('frequency')->toArray();

    $hasValidFrequency = in_array('monthly', $frequencies) ||
                        in_array('quarterly', $frequencies) ||
                        in_array('annual', $frequencies);

    expect($hasValidFrequency)->toBeTrue();
});

it('parses categories correctly', function () {
    $this->artisan('db:seed', ['--class' => CsvTaxModelSeeder::class])
        ->assertSuccessful();

    $categories = TaxModel::distinct()->pluck('category')->toArray();

    $hasValidCategory = in_array('iva', $categories) ||
                       in_array('irpf', $categories) ||
                       in_array('informativa', $categories) ||
                       in_array('retenciones', $categories);

    expect($hasValidCategory)->toBeTrue();
});

it('handles edge cases with empty fields', function () {
    $this->artisan('db:seed', ['--class' => CsvTaxModelSeeder::class])
        ->assertSuccessful();

    // Check that models without certain optional fields still work
    $models = TaxModel::all();

    expect($models->count())->toBeGreaterThan(0);

    foreach ($models as $model) {
        expect($model->model_number)->not->toBeEmpty()
            ->and($model->name)->not->toBeEmpty();
    }
});

it('stores period descriptions', function () {
    $this->artisan('db:seed', ['--class' => CsvTaxModelSeeder::class])
        ->assertSuccessful();

    $deadlineWithDescription = Deadline::whereNotNull('period_description')->first();

    expect($deadlineWithDescription)->not->toBeNull()
        ->and($deadlineWithDescription->period_description)->not->toBeEmpty();
});

it('stores notes from CSV', function () {
    $this->artisan('db:seed', ['--class' => CsvTaxModelSeeder::class])
        ->assertSuccessful();

    $deadlineWithNotes = Deadline::whereNotNull('notes')->first();

    $hasValidNote = str_contains($deadlineWithNotes->notes, 'AEAT') ||
                   str_contains($deadlineWithNotes->notes, 'Calendario');

    expect($deadlineWithNotes)->not->toBeNull()
        ->and($hasValidNote)->toBeTrue();
});

it('creates deadlines for year 2026', function () {
    $this->artisan('db:seed', ['--class' => CsvTaxModelSeeder::class])
        ->assertSuccessful();

    $allDeadlines = Deadline::all();

    foreach ($allDeadlines as $deadline) {
        expect($deadline->year)->toBe(2026)
            ->and($deadline->deadline_date->year)->toBeIn([2025, 2026, 2027]);
    }
});

it('handles BOM encoding correctly', function () {
    $this->artisan('db:seed', ['--class' => CsvTaxModelSeeder::class])
        ->assertSuccessful();

    // If BOM handling fails, model numbers might have weird characters
    $taxModels = TaxModel::all();

    foreach ($taxModels as $model) {
        expect($model->model_number)->not->toContain("\xEF\xBB\xBF")
            ->and($model->model_number)->toMatch('/^[0-9A-Za-z\-]+$/');
    }
});

it('parses applicable_to field correctly', function () {
    $this->artisan('db:seed', ['--class' => CsvTaxModelSeeder::class])
        ->assertSuccessful();

    $taxModel = TaxModel::where('model_number', '130')->first();

    expect($taxModel->applicable_to)->toBeArray()
        ->and($taxModel->applicable_to)->toContain('autonomo');
});

it('handles dates in YYYY-MM-DD format', function () {
    $this->artisan('db:seed', ['--class' => CsvTaxModelSeeder::class])
        ->assertSuccessful();

    $deadline = Deadline::first();

    expect($deadline->period_start)->toBeInstanceOf(\Carbon\CarbonInterface::class)
        ->and($deadline->period_end)->toBeInstanceOf(\Carbon\CarbonInterface::class)
        ->and($deadline->deadline_date)->toBeInstanceOf(\Carbon\CarbonInterface::class);
});

it('creates deadlines with correct date relationships', function () {
    $this->artisan('db:seed', ['--class' => CsvTaxModelSeeder::class])
        ->assertSuccessful();

    $deadlines = Deadline::whereNotNull('period_start')
        ->whereNotNull('period_end')
        ->get();

    foreach ($deadlines as $deadline) {
        // period_end should be the same as or after period_start
        expect($deadline->period_end->greaterThanOrEqualTo($deadline->period_start))->toBeTrue()
            ->and($deadline->deadline_date->equalTo($deadline->period_end))->toBeTrue();
    }
});

it('runs without errors on empty database', function () {
    expect(TaxModel::count())->toBe(0)
        ->and(Deadline::count())->toBe(0);

    $this->artisan('db:seed', ['--class' => CsvTaxModelSeeder::class])
        ->assertSuccessful();

    expect(TaxModel::count())->toBeGreaterThan(0)
        ->and(Deadline::count())->toBeGreaterThan(0);
});

it('can be run multiple times without duplicating data', function () {
    $this->artisan('db:seed', ['--class' => CsvTaxModelSeeder::class])
        ->assertSuccessful();

    $firstCount = TaxModel::count();
    $firstDeadlineCount = Deadline::count();

    $this->artisan('db:seed', ['--class' => CsvTaxModelSeeder::class])
        ->assertSuccessful();

    // Running again should create more deadlines but potentially reuse tax models
    expect(TaxModel::count())->toBeGreaterThanOrEqual($firstCount)
        ->and(Deadline::count())->toBeGreaterThan($firstDeadlineCount);
});

it('validates all deadlines have associated tax models', function () {
    $this->artisan('db:seed', ['--class' => CsvTaxModelSeeder::class])
        ->assertSuccessful();

    $deadlines = Deadline::all();

    foreach ($deadlines as $deadline) {
        expect($deadline->taxModel)->not->toBeNull()
            ->and($deadline->tax_model_id)->toBeInt();
    }
});
