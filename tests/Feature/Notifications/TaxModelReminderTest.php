<?php

use App\Models\TaxModel;
use App\Models\TaxModelReminder;
use App\Models\User;

it('can create a reminder for a tax model', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create();

    $reminder = TaxModelReminder::create([
        'user_id' => $user->id,
        'tax_model_id' => $taxModel->id,
        'days_before' => 7,
        'enabled' => true,
        'notification_type' => 'email',
    ]);

    expect($reminder)->toBeInstanceOf(TaxModelReminder::class);
    expect($reminder->days_before)->toBe(7);
    expect($reminder->enabled)->toBeTrue();
});

it('belongs to a user', function () {
    $user = User::factory()->create();
    $reminder = TaxModelReminder::factory()->create(['user_id' => $user->id]);

    expect($reminder->user)->toBeInstanceOf(User::class);
    expect($reminder->user->id)->toBe($user->id);
});

it('belongs to a tax model', function () {
    $taxModel = TaxModel::factory()->create();
    $reminder = TaxModelReminder::factory()->create(['tax_model_id' => $taxModel->id]);

    expect($reminder->taxModel)->toBeInstanceOf(TaxModel::class);
    expect($reminder->taxModel->id)->toBe($taxModel->id);
});

it('can be disabled', function () {
    $reminder = TaxModelReminder::factory()->disabled()->create();

    expect($reminder->enabled)->toBeFalse();
});

it('filters only enabled reminders with scope', function () {
    TaxModelReminder::factory()->count(3)->create(['enabled' => true]);
    TaxModelReminder::factory()->count(2)->create(['enabled' => false]);

    $enabledReminders = TaxModelReminder::enabled()->get();

    expect($enabledReminders)->toHaveCount(3);
    expect($enabledReminders->every(fn ($r) => $r->enabled))->toBeTrue();
});

it('can have multiple reminders for same tax model with different days', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create();

    $reminder1 = TaxModelReminder::create([
        'user_id' => $user->id,
        'tax_model_id' => $taxModel->id,
        'days_before' => 1,
        'enabled' => true,
        'notification_type' => 'email',
    ]);

    $reminder7 = TaxModelReminder::create([
        'user_id' => $user->id,
        'tax_model_id' => $taxModel->id,
        'days_before' => 7,
        'enabled' => true,
        'notification_type' => 'email',
    ]);

    expect($user->taxModelReminders)->toHaveCount(2);
    expect($reminder1->days_before)->toBe(1);
    expect($reminder7->days_before)->toBe(7);
});

it('user can access their reminders', function () {
    $user = User::factory()->create();
    TaxModelReminder::factory()->count(3)->create(['user_id' => $user->id]);

    expect($user->taxModelReminders)->toHaveCount(3);
});

it('tax model can access its reminders', function () {
    $taxModel = TaxModel::factory()->create();
    TaxModelReminder::factory()->count(3)->create(['tax_model_id' => $taxModel->id]);

    expect($taxModel->reminders)->toHaveCount(3);
});
