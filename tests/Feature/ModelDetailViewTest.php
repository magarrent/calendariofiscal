<?php

use App\Models\Deadline;
use App\Models\TaxModel;
use Livewire\Livewire;

test('model detail modal displays model number', function () {
    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303 - IVA',
        'category' => 'iva',
        'frequency' => 'monthly',
        'year' => 2026,
    ]);

    Livewire::test('calendar.model-detail', ['modelId' => $taxModel->id])
        ->assertSee('Modelo 303');
});

test('model detail modal displays name', function () {
    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303 - IVA',
        'category' => 'iva',
        'frequency' => 'monthly',
        'year' => 2026,
    ]);

    Livewire::test('calendar.model-detail', ['modelId' => $taxModel->id])
        ->assertSee('Modelo 303 - IVA');
});

test('model detail modal displays deadline date', function () {
    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303 - IVA',
        'year' => 2026,
    ]);

    $deadline = Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(10),
        'year' => 2026,
    ]);

    Livewire::test('calendar.model-detail', ['modelId' => $taxModel->id])
        ->assertSee('Plazos de presentación');
});

test('model detail modal displays description', function () {
    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303 - IVA',
        'description' => 'Este modelo es para la declaración del IVA mensual',
        'year' => 2026,
    ]);

    Livewire::test('calendar.model-detail', ['modelId' => $taxModel->id])
        ->assertSee('Este modelo es para la declaración del IVA mensual');
});

test('model detail modal displays who must file', function () {
    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303 - IVA',
        'applicable_to' => ['autonomo', 'pyme'],
        'year' => 2026,
    ]);

    Livewire::test('calendar.model-detail', ['modelId' => $taxModel->id])
        ->assertSee('Quién debe presentarlo')
        ->assertSee('Autónomos')
        ->assertSee('PYME');
});

test('model detail modal has detailed view toggle', function () {
    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303 - IVA',
        'year' => 2026,
    ]);

    Livewire::test('calendar.model-detail', ['modelId' => $taxModel->id])
        ->assertSee('Ver más detalles')
        ->assertSet('showDetails', false);
});

test('detailed view toggle shows filing instructions', function () {
    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303 - IVA',
        'instructions' => 'Instrucciones para presentar el modelo 303',
        'year' => 2026,
    ]);

    Livewire::test('calendar.model-detail', ['modelId' => $taxModel->id])
        ->call('toggleDetails')
        ->assertSet('showDetails', true)
        ->assertSee('Instrucciones de presentación')
        ->assertSee('Instrucciones para presentar el modelo 303');
});

test('detailed view toggle shows penalties', function () {
    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303 - IVA',
        'penalties' => 'Multa de 200€ por presentación fuera de plazo',
        'year' => 2026,
    ]);

    Livewire::test('calendar.model-detail', ['modelId' => $taxModel->id])
        ->call('toggleDetails')
        ->assertSet('showDetails', true)
        ->assertSee('Sanciones por incumplimiento')
        ->assertSee('Multa de 200€ por presentación fuera de plazo');
});

test('detailed view toggle shows official AEAT links', function () {
    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303 - IVA',
        'aeat_url' => 'https://sede.agenciatributaria.gob.es/modelo303',
        'year' => 2026,
    ]);

    Livewire::test('calendar.model-detail', ['modelId' => $taxModel->id])
        ->call('toggleDetails')
        ->assertSet('showDetails', true)
        ->assertSee('Enlaces oficiales')
        ->assertSee('Ver en el sitio web de la AEAT');
});

test('detailed view toggle can be turned off', function () {
    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303 - IVA',
        'instructions' => 'Instrucciones de presentación',
        'year' => 2026,
    ]);

    Livewire::test('calendar.model-detail', ['modelId' => $taxModel->id])
        ->call('toggleDetails')
        ->assertSet('showDetails', true)
        ->call('toggleDetails')
        ->assertSet('showDetails', false);
});

test('model detail modal shows multiple deadlines', function () {
    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303 - IVA',
        'year' => 2026,
    ]);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(10),
        'year' => 2026,
    ]);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(40),
        'year' => 2026,
    ]);

    Livewire::test('calendar.model-detail', ['modelId' => $taxModel->id])
        ->assertSee('Plazos de presentación');
});

test('model detail modal shows deadline with time', function () {
    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303 - IVA',
        'year' => 2026,
    ]);

    $deadline = Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(10),
        'deadline_time' => now()->setTime(14, 30),
        'year' => 2026,
    ]);

    Livewire::test('calendar.model-detail', ['modelId' => $taxModel->id])
        ->assertSee('Hora límite');
});

test('model detail modal shows past deadline badge', function () {
    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303 - IVA',
        'year' => 2026,
    ]);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->subDays(5),
        'year' => 2026,
    ]);

    Livewire::test('calendar.model-detail', ['modelId' => $taxModel->id])
        ->assertSee('Vencido');
});

test('model detail modal shows upcoming deadline badge', function () {
    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303 - IVA',
        'year' => 2026,
    ]);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(3),
        'year' => 2026,
    ]);

    Livewire::test('calendar.model-detail', ['modelId' => $taxModel->id])
        ->assertSee('Próximo');
});

test('model detail can be opened from calendar view', function () {
    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303 - IVA',
        'year' => 2026,
    ]);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(5),
        'year' => 2026,
    ]);

    Livewire::test('calendar.calendar-view')
        ->call('showModel', $taxModel->id)
        ->assertSet('selectedModelId', $taxModel->id)
        ->assertDispatched('open-modal');
});

test('model detail modal displays category badge with correct color', function () {
    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303 - IVA',
        'category' => 'iva',
        'year' => 2026,
    ]);

    Livewire::test('calendar.model-detail', ['modelId' => $taxModel->id])
        ->assertSee('Modelo 303');
});

test('model detail modal displays frequency badge', function () {
    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303 - IVA',
        'frequency' => 'monthly',
        'year' => 2026,
    ]);

    Livewire::test('calendar.model-detail', ['modelId' => $taxModel->id])
        ->assertSee('Mensual');
});

test('model detail modal displays all applicable company types', function () {
    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303 - IVA',
        'applicable_to' => ['autonomo', 'pyme', 'large_corp'],
        'year' => 2026,
    ]);

    Livewire::test('calendar.model-detail', ['modelId' => $taxModel->id])
        ->assertSee('Autónomos')
        ->assertSee('PYME')
        ->assertSee('Grandes empresas');
});
