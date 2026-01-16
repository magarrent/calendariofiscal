<?php

use App\Models\Deadline;
use App\Models\NotificationLog;
use App\Models\TaxModel;
use App\Models\TaxModelReminder;
use App\Models\User;
use App\Notifications\TaxModelDeadlineReminder;
use Illuminate\Support\Facades\Notification;

it('sends reminders for upcoming deadlines', function () {
    Notification::fake();

    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create();

    $deadline = Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(7),
    ]);

    $reminder = TaxModelReminder::factory()->create([
        'user_id' => $user->id,
        'tax_model_id' => $taxModel->id,
        'days_before' => 7,
        'enabled' => true,
    ]);

    $this->artisan('notifications:send-tax-reminders')
        ->assertSuccessful();

    Notification::assertSentTo($user, TaxModelDeadlineReminder::class);
});

it('does not send reminders for disabled reminders', function () {
    Notification::fake();

    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create();

    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(7),
    ]);

    TaxModelReminder::factory()->disabled()->create([
        'user_id' => $user->id,
        'tax_model_id' => $taxModel->id,
        'days_before' => 7,
    ]);

    $this->artisan('notifications:send-tax-reminders')
        ->assertSuccessful();

    Notification::assertNothingSent();
});

it('does not send duplicate notifications on same day', function () {
    Notification::fake();

    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create();

    $deadline = Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(7),
    ]);

    $reminder = TaxModelReminder::factory()->create([
        'user_id' => $user->id,
        'tax_model_id' => $taxModel->id,
        'days_before' => 7,
        'enabled' => true,
    ]);

    $this->artisan('notifications:send-tax-reminders')
        ->assertSuccessful();

    $this->artisan('notifications:send-tax-reminders')
        ->assertSuccessful();

    Notification::assertSentToTimes($user, TaxModelDeadlineReminder::class, 1);
});

it('logs sent notifications', function () {
    Notification::fake();

    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create();

    $deadline = Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(7),
    ]);

    $reminder = TaxModelReminder::factory()->create([
        'user_id' => $user->id,
        'tax_model_id' => $taxModel->id,
        'days_before' => 7,
        'enabled' => true,
    ]);

    $this->artisan('notifications:send-tax-reminders')
        ->assertSuccessful();

    expect(NotificationLog::count())->toBe(1);

    $log = NotificationLog::first();
    expect($log->user_id)->toBe($user->id);
    expect($log->tax_model_id)->toBe($taxModel->id);
    expect($log->notification_type)->toBe('email');
});

it('sends multiple reminders for different days before', function () {
    Notification::fake();

    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create();

    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(7),
    ]);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(30),
    ]);

    TaxModelReminder::factory()->create([
        'user_id' => $user->id,
        'tax_model_id' => $taxModel->id,
        'days_before' => 7,
        'enabled' => true,
    ]);

    TaxModelReminder::factory()->create([
        'user_id' => $user->id,
        'tax_model_id' => $taxModel->id,
        'days_before' => 30,
        'enabled' => true,
    ]);

    $this->artisan('notifications:send-tax-reminders')
        ->assertSuccessful();

    Notification::assertSentToTimes($user, TaxModelDeadlineReminder::class, 2);
});
