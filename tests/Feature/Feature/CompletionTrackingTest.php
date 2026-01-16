<?php

use App\Models\Deadline;
use App\Models\TaxModel;
use App\Models\User;
use App\Models\UserModelCompletion;
use Livewire\Livewire;

test('user can toggle model completion', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create(['year' => 2026]);

    $this->actingAs($user);

    expect($user->hasCompletedModel($taxModel, 2026))->toBeFalse();

    $user->toggleModelCompletion($taxModel, 2026);

    expect($user->hasCompletedModel($taxModel, 2026))->toBeTrue();

    $completion = UserModelCompletion::where('user_id', $user->id)
        ->where('tax_model_id', $taxModel->id)
        ->where('year', 2026)
        ->first();

    expect($completion)->not->toBeNull()
        ->and($completion->completed)->toBeTrue()
        ->and($completion->completed_at)->not->toBeNull();
});

test('user can undo model completion', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create(['year' => 2026]);

    $this->actingAs($user);

    $user->toggleModelCompletion($taxModel, 2026);
    expect($user->hasCompletedModel($taxModel, 2026))->toBeTrue();

    $user->toggleModelCompletion($taxModel, 2026);
    expect($user->hasCompletedModel($taxModel, 2026))->toBeFalse();

    $completion = UserModelCompletion::where('user_id', $user->id)
        ->where('tax_model_id', $taxModel->id)
        ->where('year', 2026)
        ->first();

    expect($completion->completed)->toBeFalse()
        ->and($completion->completed_at)->toBeNull();
});

test('model completion component displays correct status', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create(['year' => 2026]);

    $this->actingAs($user);

    Livewire::test('calendar.model-completion', [
        'taxModelId' => $taxModel->id,
        'year' => 2026,
    ])
        ->assertSet('isCompleted', false)
        ->assertSee('Pendiente');
});

test('model completion component can toggle completion', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create(['year' => 2026]);

    $this->actingAs($user);

    Livewire::test('calendar.model-completion', [
        'taxModelId' => $taxModel->id,
        'year' => 2026,
    ])
        ->call('toggleCompletion')
        ->assertSet('isCompleted', true)
        ->assertSee('Completado');
});

test('completed models are visually distinguished in calendar', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create(['year' => 2026]);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(5),
        'year' => 2026,
    ]);

    $this->actingAs($user);
    $user->toggleModelCompletion($taxModel, 2026);

    $response = $this->get('/');

    $response->assertSee('Completado');
});

test('calendar filter shows only incomplete obligations', function () {
    $user = User::factory()->create();

    $completedModel = TaxModel::factory()->create(['year' => 2026, 'name' => 'Completed Model']);
    $incompleteModel = TaxModel::factory()->create(['year' => 2026, 'name' => 'Incomplete Model']);

    Deadline::factory()->create([
        'tax_model_id' => $completedModel->id,
        'deadline_date' => now()->addDays(5),
        'year' => 2026,
    ]);

    Deadline::factory()->create([
        'tax_model_id' => $incompleteModel->id,
        'deadline_date' => now()->addDays(10),
        'year' => 2026,
    ]);

    $this->actingAs($user);
    $user->toggleModelCompletion($completedModel, 2026);

    $component = Livewire::test('calendar.calendar-view')
        ->set('showOnlyIncomplete', true);

    $deadlines = $component->get('filteredDeadlines');

    expect($deadlines->count())->toBe(1)
        ->and($deadlines->first()->taxModel->name)->toBe('Incomplete Model');
});

test('completion history page shows completed models', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create(['year' => 2026]);

    $this->actingAs($user);
    $user->toggleModelCompletion($taxModel, 2026);

    $response = $this->get('/settings/completion-history');

    $response->assertSuccessful()
        ->assertSee($taxModel->name)
        ->assertSee('Completado');
});

test('completion history shows empty state when no completions', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $response = $this->get('/settings/completion-history');

    $response->assertSuccessful()
        ->assertSee('No hay modelos completados');
});

test('user can undo completion from history', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create(['year' => 2026]);

    $this->actingAs($user);
    $user->toggleModelCompletion($taxModel, 2026);

    $completion = UserModelCompletion::where('user_id', $user->id)
        ->where('tax_model_id', $taxModel->id)
        ->first();

    Livewire::test('settings.completion-history')
        ->call('undoCompletion', $completion->id);

    expect($user->fresh()->hasCompletedModel($taxModel, 2026))->toBeFalse();
});

test('completion is specific to year', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create(['year' => 2026]);

    $this->actingAs($user);

    $user->toggleModelCompletion($taxModel, 2026);
    expect($user->hasCompletedModel($taxModel, 2026))->toBeTrue();
    expect($user->hasCompletedModel($taxModel, 2025))->toBeFalse();
});

test('completion is specific to user', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $taxModel = TaxModel::factory()->create(['year' => 2026]);

    $user1->toggleModelCompletion($taxModel, 2026);

    expect($user1->hasCompletedModel($taxModel, 2026))->toBeTrue()
        ->and($user2->hasCompletedModel($taxModel, 2026))->toBeFalse();
});

test('guest cannot see completion toggle', function () {
    $taxModel = TaxModel::factory()->create(['year' => 2026]);

    Livewire::test('calendar.model-completion', [
        'taxModelId' => $taxModel->id,
        'year' => 2026,
    ])
        ->assertSet('isCompleted', false);
});

test('guest cannot access completion history', function () {
    $response = $this->get('/settings/completion-history');

    $response->assertRedirect('/login');
});

test('completion filter is cleared when clearing all filters', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('calendar.calendar-view')
        ->set('showOnlyIncomplete', true)
        ->set('categories', ['iva'])
        ->call('clearFilters')
        ->assertSet('showOnlyIncomplete', false)
        ->assertSet('categories', []);
});

test('completed models have correct timestamps', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create(['year' => 2026]);

    $this->actingAs($user);

    $user->toggleModelCompletion($taxModel, 2026);

    $completion = UserModelCompletion::where('user_id', $user->id)
        ->where('tax_model_id', $taxModel->id)
        ->first();

    expect($completion->completed_at)->not->toBeNull()
        ->and($completion->completed_at->diffInSeconds(now()))->toBeLessThan(5);
});

test('model detail modal shows completion status', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create(['year' => 2026]);

    $this->actingAs($user);
    $user->toggleModelCompletion($taxModel, 2026);

    Livewire::test('calendar.model-detail', ['modelId' => $taxModel->id])
        ->assertSee('Estado de cumplimiento')
        ->assertSee('Completado');
});

test('completion status persists across page reloads', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create(['year' => 2026]);

    $this->actingAs($user);
    $user->toggleModelCompletion($taxModel, 2026);

    expect($user->fresh()->hasCompletedModel($taxModel, 2026))->toBeTrue();
});

test('multiple completions can exist for same model in different years', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create(['year' => 2026]);

    $this->actingAs($user);

    $user->toggleModelCompletion($taxModel, 2026);
    $user->toggleModelCompletion($taxModel, 2025);

    expect(UserModelCompletion::where('user_id', $user->id)
        ->where('tax_model_id', $taxModel->id)
        ->where('completed', true)
        ->count())->toBe(2);
});
