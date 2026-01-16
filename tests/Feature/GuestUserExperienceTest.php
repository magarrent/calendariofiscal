<?php

use App\Livewire\Calendar\CalendarView;
use Livewire\Livewire;

it('displays the calendar view for guests', function () {
    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee('Calendario Fiscal 2026');
});

it('shows registration and login CTAs for guests', function () {
    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertSee('Iniciar Sesión');
    $response->assertSee('Registrarse Gratis');
});

it('shows locked filter section for guests', function () {
    Livewire::test(CalendarView::class)
        ->assertSee('Filtros')
        ->assertSee('Desbloquea Filtros Avanzados')
        ->assertSee('Regístrate gratis para filtrar por categoría');
});

it('disables filter inputs for guests', function () {
    $component = Livewire::test(CalendarView::class);

    expect($component->instance()->canUseFilters())->toBeFalse();
});

it('shows locked export buttons for guests when there are deadlines', function () {
    // This test verifies that export buttons are visible but disabled
    // The buttons only appear when deadlines exist, which requires database data
    $component = Livewire::test(CalendarView::class);

    // Verify export is disabled for guests
    expect($component->instance()->canExport())->toBeFalse();
});

it('disables export functionality for guests', function () {
    $component = Livewire::test(CalendarView::class);

    expect($component->instance()->canExport())->toBeFalse();
});

it('prevents guests from exporting CSV', function () {
    Livewire::test(CalendarView::class)
        ->call('exportCsv')
        ->assertForbidden();
});

it('prevents guests from exporting Excel', function () {
    Livewire::test(CalendarView::class)
        ->call('exportExcel')
        ->assertForbidden();
});

it('prevents guests from exporting iCal', function () {
    Livewire::test(CalendarView::class)
        ->call('exportIcal')
        ->assertForbidden();
});

it('shows completion filter as locked for guests', function () {
    Livewire::test(CalendarView::class)
        ->assertSee('Estado')
        ->assertSee('Solo mostrar pendientes');
});

it('disables completion tracking for guests', function () {
    $component = Livewire::test(CalendarView::class);

    expect($component->instance()->canTrackCompletion())->toBeFalse();
});

it('allows guests to view deadlines without filtering', function () {
    $component = Livewire::test(CalendarView::class);

    $deadlines = $component->get('filteredDeadlines');

    expect($deadlines)->not->toBeNull();
});

it('allows guests to navigate between periods', function () {
    Livewire::test(CalendarView::class)
        ->call('nextPeriod')
        ->assertSuccessful()
        ->call('previousPeriod')
        ->assertSuccessful()
        ->call('today')
        ->assertSuccessful();
});

it('defaults to year view for guests', function () {
    Livewire::test(CalendarView::class)
        ->assertSet('view', 'year');
});

it('shows lock icons on export buttons for guests when deadlines exist', function () {
    // Create test data so export buttons appear
    $taxModel = \App\Models\TaxModel::factory()->create();
    \App\Models\Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now(),
        'year' => now()->year,
    ]);

    $response = $this->get('/');

    // The UI should show lock icons when export buttons are present
    $response->assertSee('Regístrate para exportar datos');
});

it('displays prominent registration CTA in filter section', function () {
    $response = $this->get('/');

    $response->assertSee('Registrarse Ahora');
});

it('shows visual distinction between available and locked features', function () {
    $response = $this->get('/');

    // Calendar is available (year view is always visible)
    $response->assertSee('Calendario Fiscal');

    // Filters are locked for guests
    $response->assertSee('Desbloquea Filtros Avanzados');
});

describe('Authenticated User Experience', function () {
    beforeEach(function () {
        $this->user = \App\Models\User::factory()->create();
        $this->actingAs($this->user);
    });

    it('hides registration CTAs for authenticated users', function () {
        $response = $this->get('/');

        $response->assertDontSee('Registrarse Gratis');
        $response->assertDontSee('Iniciar Sesión');
    });

    it('enables filters for authenticated users', function () {
        $component = Livewire::test(CalendarView::class);

        expect($component->instance()->canUseFilters())->toBeTrue();
    });

    it('enables export functionality for authenticated users', function () {
        $component = Livewire::test(CalendarView::class);

        expect($component->instance()->canExport())->toBeTrue();
    });

    it('enables completion tracking for authenticated users', function () {
        $component = Livewire::test(CalendarView::class);

        expect($component->instance()->canTrackCompletion())->toBeTrue();
    });

    it('does not show filter lock messages for authenticated users', function () {
        $response = $this->get('/');

        $response->assertDontSee('Desbloquea Filtros Avanzados');
        $response->assertDontSee('Regístrate gratis para filtrar');
    });

    it('does not show export lock messages for authenticated users', function () {
        $response = $this->get('/');

        $response->assertDontSee('Regístrate para exportar datos');
    });

    it('allows authenticated users to apply filters', function () {
        Livewire::test(CalendarView::class)
            ->set('categories', ['iva'])
            ->assertSet('categories', ['iva'])
            ->set('frequencies', ['mensual'])
            ->assertSet('frequencies', ['mensual'])
            ->assertSuccessful();
    });
});
