<?php

use App\Livewire\Notifications\ManageReminders;
use App\Models\TaxModel;
use App\Models\TaxModelReminder;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;

it('loads reminders for a tax model', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create();

    TaxModelReminder::factory()->create([
        'user_id' => $user->id,
        'tax_model_id' => $taxModel->id,
        'days_before' => 7,
        'enabled' => true,
    ]);

    actingAs($user);

    Livewire::test(ManageReminders::class, ['taxModelId' => $taxModel->id])
        ->assertSet('reminders.7.enabled', true)
        ->assertSet('reminders.7.days_before', 7);
});

it('can toggle a reminder', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create();

    actingAs($user);

    Livewire::test(ManageReminders::class, ['taxModelId' => $taxModel->id])
        ->call('toggleReminder', 7)
        ->assertSet('reminders.7.enabled', true);

    expect(TaxModelReminder::where('user_id', $user->id)
        ->where('tax_model_id', $taxModel->id)
        ->where('days_before', 7)
        ->first()
    )->not->toBeNull();
});

it('can toggle all reminders at once', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create();

    actingAs($user);

    Livewire::test(ManageReminders::class, ['taxModelId' => $taxModel->id])
        ->call('toggleAll')
        ->assertSet('allEnabled', true)
        ->assertSet('reminders.1.enabled', true)
        ->assertSet('reminders.7.enabled', true)
        ->assertSet('reminders.15.enabled', true)
        ->assertSet('reminders.30.enabled', true);

    expect(TaxModelReminder::where('user_id', $user->id)
        ->where('tax_model_id', $taxModel->id)
        ->count()
    )->toBe(4);
});

it('can disable a reminder', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create();

    $reminder = TaxModelReminder::factory()->create([
        'user_id' => $user->id,
        'tax_model_id' => $taxModel->id,
        'days_before' => 7,
        'enabled' => true,
    ]);

    actingAs($user);

    Livewire::test(ManageReminders::class, ['taxModelId' => $taxModel->id])
        ->call('toggleReminder', 7)
        ->assertSet('reminders.7.enabled', false);

    expect($reminder->fresh()->enabled)->toBeFalse();
});

it('shows preset days', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create();

    actingAs($user);

    Livewire::test(ManageReminders::class, ['taxModelId' => $taxModel->id])
        ->assertSet('presetDays', [1, 7, 15, 30])
        ->assertSee('1 día antes')
        ->assertSee('7 días antes')
        ->assertSee('15 días antes')
        ->assertSee('30 días antes');
});

it('can apply reminders to category', function () {
    $user = User::factory()->create();
    $taxModel1 = TaxModel::factory()->create(['category' => 'iva']);
    $taxModel2 = TaxModel::factory()->create(['category' => 'iva']);
    $taxModel3 = TaxModel::factory()->create(['category' => 'irpf']);

    actingAs($user);

    Livewire::test(ManageReminders::class, ['taxModelId' => $taxModel1->id])
        ->call('toggleReminder', 7)
        ->call('applyToCategory', 'iva');

    expect(TaxModelReminder::where('user_id', $user->id)
        ->where('tax_model_id', $taxModel2->id)
        ->where('days_before', 7)
        ->exists()
    )->toBeTrue();

    expect(TaxModelReminder::where('user_id', $user->id)
        ->where('tax_model_id', $taxModel3->id)
        ->exists()
    )->toBeFalse();
});
