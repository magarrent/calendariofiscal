<?php

use App\Exports\DeadlinesExport;
use App\Models\Deadline;
use App\Models\TaxModel;
use Livewire\Livewire;

test('calendar exports to csv with filtered results', function () {
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

    $response = Livewire::test('calendar.calendar-view')
        ->call('exportCsv');

    $response->assertStatus(200);
});

test('calendar exports to excel with filtered results', function () {
    $taxModel = TaxModel::factory()->create([
        'model_number' => '130',
        'name' => 'Modelo 130 - IRPF',
        'category' => 'irpf',
        'frequency' => 'quarterly',
        'year' => 2026,
    ]);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(10),
        'year' => 2026,
    ]);

    $response = Livewire::test('calendar.calendar-view')
        ->call('exportExcel');

    $response->assertStatus(200);
});

test('calendar exports to ical with filtered results', function () {
    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303 - IVA',
        'description' => 'Declaración de IVA trimestral',
        'category' => 'iva',
        'frequency' => 'quarterly',
        'aeat_url' => 'https://sede.agenciatributaria.gob.es/modelo303',
        'year' => 2026,
    ]);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(15),
        'deadline_time' => now()->setTime(14, 0),
        'year' => 2026,
    ]);

    $response = Livewire::test('calendar.calendar-view')
        ->call('exportIcal');

    $response->assertStatus(200);
});

test('csv export includes all relevant deadline data', function () {
    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303 - IVA',
        'description' => 'Declaración de IVA',
        'category' => 'iva',
        'frequency' => 'monthly',
        'applicable_to' => ['autonomo', 'pyme'],
        'aeat_url' => 'https://sede.agenciatributaria.gob.es',
        'year' => 2026,
    ]);

    $deadline = Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(5),
        'deadline_time' => now()->setTime(14, 0),
        'notes' => 'Test notes',
        'year' => 2026,
    ]);

    $export = new DeadlinesExport(collect([$deadline->load('taxModel')]));
    $data = $export->map($deadline);

    expect($data)->toContain('303')
        ->and($data)->toContain('Modelo 303 - IVA')
        ->and($data)->toContain('iva')
        ->and($data)->toContain('monthly')
        ->and($data)->toContain('autonomo, pyme')
        ->and($data)->toContain('Test notes')
        ->and($data)->toContain('https://sede.agenciatributaria.gob.es');
});

test('csv export has correct spanish headers', function () {
    $export = new DeadlinesExport(collect());
    $headers = $export->headings();

    expect($headers)->toContain('Número de Modelo')
        ->and($headers)->toContain('Nombre del Modelo')
        ->and($headers)->toContain('Categoría')
        ->and($headers)->toContain('Periodicidad')
        ->and($headers)->toContain('Fecha Límite')
        ->and($headers)->toContain('Hora Límite')
        ->and($headers)->toContain('Año')
        ->and($headers)->toContain('Aplicable a')
        ->and($headers)->toContain('Notas')
        ->and($headers)->toContain('URL AEAT');
});

test('export reflects currently applied category filter', function () {
    $taxModel1 = TaxModel::factory()->create([
        'category' => 'iva',
        'year' => 2026,
    ]);

    $taxModel2 = TaxModel::factory()->create([
        'category' => 'irpf',
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

    $response = $component->call('exportCsv');
    $response->assertStatus(200);
});

test('export reflects currently applied frequency filter', function () {
    $taxModel1 = TaxModel::factory()->create([
        'frequency' => 'monthly',
        'year' => 2026,
    ]);

    $taxModel2 = TaxModel::factory()->create([
        'frequency' => 'quarterly',
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
        ->set('frequencies', ['monthly']);

    expect($component->get('filteredDeadlines')->count())->toBe(1);

    $response = $component->call('exportExcel');
    $response->assertStatus(200);
});

test('export reflects currently applied company type filter', function () {
    $taxModel1 = TaxModel::factory()->create([
        'applicable_to' => ['autonomo', 'pyme'],
        'year' => 2026,
    ]);

    $taxModel2 = TaxModel::factory()->create([
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

    $component = Livewire::test('calendar.calendar-view')
        ->set('companyTypes', ['autonomo']);

    expect($component->get('filteredDeadlines')->count())->toBe(1);

    $response = $component->call('exportCsv');
    $response->assertStatus(200);
});

test('export reflects multiple applied filters', function () {
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

    $taxModel3 = TaxModel::factory()->create([
        'category' => 'iva',
        'frequency' => 'quarterly',
        'applicable_to' => ['autonomo'],
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

    Deadline::factory()->create([
        'tax_model_id' => $taxModel3->id,
        'deadline_date' => now()->addDays(15),
        'year' => 2026,
    ]);

    $component = Livewire::test('calendar.calendar-view')
        ->set('categories', ['iva'])
        ->set('frequencies', ['monthly'])
        ->set('companyTypes', ['autonomo']);

    expect($component->get('filteredDeadlines')->count())->toBe(1);

    $response = $component->call('exportExcel');
    $response->assertStatus(200);
});

test('export buttons only show when deadlines exist', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertDontSee('Exportar CSV');
});

test('export buttons show when deadlines exist', function () {
    $taxModel = TaxModel::factory()->create(['year' => 2026]);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(5),
        'year' => 2026,
    ]);

    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertSee('Exportar CSV');
    $response->assertSee('Exportar Excel');
    $response->assertSee('Exportar iCal');
});

test('ical export handles deadlines with time correctly', function () {
    $taxModel = TaxModel::factory()->create(['year' => 2026]);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(5),
        'deadline_time' => now()->setTime(14, 30),
        'year' => 2026,
    ]);

    $response = Livewire::test('calendar.calendar-view')
        ->call('exportIcal');

    $response->assertStatus(200);
});

test('ical export handles deadlines without time as full day events', function () {
    $taxModel = TaxModel::factory()->create(['year' => 2026]);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(5),
        'deadline_time' => null,
        'year' => 2026,
    ]);

    $response = Livewire::test('calendar.calendar-view')
        ->call('exportIcal');

    $response->assertStatus(200);
});
