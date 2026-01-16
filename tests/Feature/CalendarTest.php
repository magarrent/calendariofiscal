<?php

use App\Models\Deadline;
use App\Models\TaxModel;
use Livewire\Livewire;

test('calendar index page is accessible without authentication', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Calendario Fiscal 2026');
});

test('calendar displays tax models and deadlines', function () {
    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303 - IVA',
        'category' => 'iva',
        'frequency' => 'monthly',
        'year' => 2026,
    ]);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(5),
        'year' => 2026,
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Calendario Fiscal 2026');
});

test('calendar supports month view', function () {
    Livewire::test('calendar.calendar-view')
        ->set('view', 'month')
        ->assertSet('view', 'month')
        ->assertStatus(200);
});

test('calendar supports week view', function () {
    Livewire::test('calendar.calendar-view')
        ->set('view', 'week')
        ->assertSet('view', 'week')
        ->assertStatus(200);
});

test('calendar supports day view', function () {
    Livewire::test('calendar.calendar-view')
        ->set('view', 'day')
        ->assertSet('view', 'day')
        ->assertStatus(200);
});

test('calendar supports list view', function () {
    Livewire::test('calendar.calendar-view')
        ->set('view', 'list')
        ->assertSet('view', 'list')
        ->assertStatus(200);
});

test('calendar supports timeline view', function () {
    Livewire::test('calendar.calendar-view')
        ->set('view', 'timeline')
        ->assertSet('view', 'timeline')
        ->assertStatus(200);
});

test('calendar supports year view', function () {
    Livewire::test('calendar.calendar-view')
        ->set('view', 'year')
        ->assertSet('view', 'year')
        ->assertStatus(200);
});

test('calendar filters by category', function () {
    TaxModel::factory()->create(['category' => 'iva', 'year' => 2026]);
    TaxModel::factory()->create(['category' => 'irpf', 'year' => 2026]);

    Livewire::test('calendar.calendar-view')
        ->set('categories', ['iva'])
        ->assertSet('categories', ['iva']);
});

test('calendar filters by frequency', function () {
    TaxModel::factory()->create(['frequency' => 'monthly', 'year' => 2026]);
    TaxModel::factory()->create(['frequency' => 'quarterly', 'year' => 2026]);

    Livewire::test('calendar.calendar-view')
        ->set('frequencies', ['monthly'])
        ->assertSet('frequencies', ['monthly']);
});

test('calendar navigation works', function () {
    Livewire::test('calendar.calendar-view')
        ->call('today')
        ->call('nextPeriod')
        ->call('previousPeriod')
        ->assertStatus(200);
});

test('calendar clear filters works', function () {
    Livewire::test('calendar.calendar-view')
        ->set('categories', ['iva'])
        ->set('frequencies', ['monthly'])
        ->call('clearFilters')
        ->assertSet('categories', [])
        ->assertSet('frequencies', []);
});

test('calendar filters by company type', function () {
    $taxModel1 = TaxModel::factory()->create([
        'category' => 'iva',
        'frequency' => 'monthly',
        'applicable_to' => ['autonomo', 'pyme'],
        'year' => 2026,
    ]);

    $taxModel2 = TaxModel::factory()->create([
        'category' => 'irpf',
        'frequency' => 'annual',
        'applicable_to' => ['gran_empresa'],
        'year' => 2026,
    ]);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel1->id,
        'deadline_date' => now()->addDays(5),
        'year' => 2026,
    ]);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel2->id,
        'deadline_date' => now()->addDays(10),
        'year' => 2026,
    ]);

    Livewire::test('calendar.calendar-view')
        ->set('companyTypes', ['autonomo'])
        ->assertSet('companyTypes', ['autonomo']);
});

test('calendar filters by proximity next 7 days', function () {
    $taxModel = TaxModel::factory()->create(['year' => 2026]);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(5),
        'year' => 2026,
    ]);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(15),
        'year' => 2026,
    ]);

    Livewire::test('calendar.calendar-view')
        ->set('proximity', 'next_7_days')
        ->assertSet('proximity', 'next_7_days');
});

test('calendar filters by proximity next 30 days', function () {
    Livewire::test('calendar.calendar-view')
        ->set('proximity', 'next_30_days')
        ->assertSet('proximity', 'next_30_days');
});

test('calendar filters by proximity next 60 days', function () {
    Livewire::test('calendar.calendar-view')
        ->set('proximity', 'next_60_days')
        ->assertSet('proximity', 'next_60_days');
});

test('calendar filters by proximity next 90 days', function () {
    Livewire::test('calendar.calendar-view')
        ->set('proximity', 'next_90_days')
        ->assertSet('proximity', 'next_90_days');
});

test('calendar applies multiple filters simultaneously', function () {
    $taxModel1 = TaxModel::factory()->create([
        'category' => 'iva',
        'frequency' => 'monthly',
        'applicable_to' => ['autonomo'],
        'year' => 2026,
    ]);

    $taxModel2 = TaxModel::factory()->create([
        'category' => 'irpf',
        'frequency' => 'quarterly',
        'applicable_to' => ['pyme'],
        'year' => 2026,
    ]);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel1->id,
        'deadline_date' => now()->addDays(5),
        'year' => 2026,
    ]);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel2->id,
        'deadline_date' => now()->addDays(10),
        'year' => 2026,
    ]);

    Livewire::test('calendar.calendar-view')
        ->set('categories', ['iva'])
        ->set('frequencies', ['monthly'])
        ->set('companyTypes', ['autonomo'])
        ->set('proximity', 'next_7_days')
        ->assertSet('categories', ['iva'])
        ->assertSet('frequencies', ['monthly'])
        ->assertSet('companyTypes', ['autonomo'])
        ->assertSet('proximity', 'next_7_days');
});

test('calendar clears all filter types', function () {
    Livewire::test('calendar.calendar-view')
        ->set('categories', ['iva'])
        ->set('frequencies', ['monthly'])
        ->set('companyTypes', ['autonomo'])
        ->set('proximity', 'next_30_days')
        ->call('clearFilters')
        ->assertSet('categories', [])
        ->assertSet('frequencies', [])
        ->assertSet('companyTypes', [])
        ->assertSet('proximity', null);
});

test('calendar shows filtered deadlines correctly', function () {
    $taxModel1 = TaxModel::factory()->create([
        'name' => 'Modelo 303 - IVA',
        'category' => 'iva',
        'frequency' => 'monthly',
        'year' => 2026,
    ]);

    $taxModel2 = TaxModel::factory()->create([
        'name' => 'Modelo 100 - IRPF',
        'category' => 'irpf',
        'frequency' => 'annual',
        'year' => 2026,
    ]);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel1->id,
        'deadline_date' => now()->addDays(5),
        'year' => 2026,
    ]);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel2->id,
        'deadline_date' => now()->addDays(10),
        'year' => 2026,
    ]);

    $component = Livewire::test('calendar.calendar-view')
        ->set('categories', ['iva']);

    expect($component->get('filteredDeadlines')->count())->toBe(1);
});

test('calendar filters persist during session', function () {
    $component = Livewire::test('calendar.calendar-view')
        ->set('categories', ['iva'])
        ->set('frequencies', ['monthly']);

    $component->assertSet('categories', ['iva'])
        ->assertSet('frequencies', ['monthly']);
});
