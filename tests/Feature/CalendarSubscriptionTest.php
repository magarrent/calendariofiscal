<?php

use App\Models\Deadline;
use App\Models\TaxModel;
use App\Models\User;
use Livewire\Livewire;

test('user can generate subscription token', function () {
    $user = User::factory()->create();

    expect($user->subscription_token)->toBeNull();

    $token = $user->generateSubscriptionToken();

    expect($token)->not->toBeNull()
        ->and($token)->toHaveLength(64)
        ->and($user->fresh()->subscription_token)->toBe($token);
});

test('user can regenerate subscription token', function () {
    $user = User::factory()->create();

    $firstToken = $user->generateSubscriptionToken();
    $secondToken = $user->regenerateSubscriptionToken();

    expect($firstToken)->not->toBe($secondToken)
        ->and($secondToken)->toHaveLength(64)
        ->and($user->fresh()->subscription_token)->toBe($secondToken);
});

test('subscription tokens are unique', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $token1 = $user1->generateSubscriptionToken();
    $token2 = $user2->generateSubscriptionToken();

    expect($token1)->not->toBe($token2);
});

test('user can get subscription url', function () {
    $user = User::factory()->create();

    expect($user->subscriptionUrl())->toBeNull();

    $user->generateSubscriptionToken();

    $url = $user->subscriptionUrl();

    expect($url)->not->toBeNull()
        ->and($url)->toContain('/calendar/subscription/')
        ->and($url)->toContain($user->subscription_token);
});

test('subscription feed returns ical format', function () {
    $user = User::factory()->create();
    $token = $user->generateSubscriptionToken();

    $response = $this->get(route('calendar.subscription.feed', ['token' => $token]));

    $response->assertSuccessful()
        ->assertHeader('Content-Type', 'text/calendar; charset=utf-8');
});

test('subscription feed requires valid token', function () {
    $response = $this->get(route('calendar.subscription.feed', ['token' => 'invalid-token']));

    $response->assertNotFound();
});

test('subscription feed includes user favorite models', function () {
    $user = User::factory()->create([
        'company_type' => 'autonomo',
    ]);
    $token = $user->generateSubscriptionToken();

    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303 - IVA',
        'description' => 'Declaración de IVA',
        'category' => 'iva',
        'frequency' => 'monthly',
        'applicable_to' => ['autonomo', 'pyme'],
        'year' => now()->year,
    ]);

    $user->favoriteTaxModels()->attach($taxModel);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(30),
        'year' => now()->year,
    ]);

    $response = $this->get(route('calendar.subscription.feed', ['token' => $token]));

    $response->assertSuccessful();

    $content = $response->streamedContent();
    expect($content)->toContain('Modelo 303')
        ->and($content)->toContain('BEGIN:VCALENDAR')
        ->and($content)->toContain('BEGIN:VEVENT')
        ->and($content)->toContain('END:VEVENT')
        ->and($content)->toContain('END:VCALENDAR');
});

test('subscription feed includes models applicable to user company type', function () {
    $user = User::factory()->create([
        'company_type' => 'pyme',
    ]);
    $token = $user->generateSubscriptionToken();

    $taxModel = TaxModel::factory()->create([
        'model_number' => '200',
        'name' => 'Modelo 200 - IS',
        'description' => 'Impuesto sobre Sociedades',
        'category' => 'sociedades',
        'frequency' => 'annual',
        'applicable_to' => ['pyme', 'large_corp'],
        'year' => now()->year,
    ]);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(60),
        'year' => now()->year,
    ]);

    $response = $this->get(route('calendar.subscription.feed', ['token' => $token]));

    $response->assertSuccessful();

    $content = $response->streamedContent();
    expect($content)->toContain('Modelo 200');
});

test('subscription feed excludes models not applicable to user', function () {
    $user = User::factory()->create([
        'company_type' => 'autonomo',
    ]);
    $token = $user->generateSubscriptionToken();

    $taxModel = TaxModel::factory()->create([
        'model_number' => '200',
        'name' => 'Modelo 200 - IS',
        'applicable_to' => ['pyme', 'large_corp'],
        'year' => now()->year,
    ]);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(60),
        'year' => now()->year,
    ]);

    $response = $this->get(route('calendar.subscription.feed', ['token' => $token]));

    $response->assertSuccessful();

    $content = $response->streamedContent();
    expect($content)->not->toContain('Modelo 200');
});

test('subscription feed includes deadlines with time', function () {
    $user = User::factory()->create();
    $token = $user->generateSubscriptionToken();

    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303',
        'applicable_to' => ['autonomo', 'pyme'],
        'year' => now()->year,
    ]);

    $user->favoriteTaxModels()->attach($taxModel);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(15),
        'deadline_time' => now()->setTime(14, 30),
        'year' => now()->year,
    ]);

    $response = $this->get(route('calendar.subscription.feed', ['token' => $token]));

    $response->assertSuccessful();

    $content = $response->streamedContent();
    expect($content)->toContain('DTSTART')
        ->and($content)->toContain('DTEND');
});

test('subscription feed includes event descriptions', function () {
    $user = User::factory()->create();
    $token = $user->generateSubscriptionToken();

    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303',
        'description' => 'Test description',
        'applicable_to' => ['autonomo'],
        'year' => now()->year,
    ]);

    $user->favoriteTaxModels()->attach($taxModel);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(20),
        'year' => now()->year,
    ]);

    $response = $this->get(route('calendar.subscription.feed', ['token' => $token]));

    $response->assertSuccessful();

    $content = $response->streamedContent();
    expect($content)->toContain('DESCRIPTION');
});

test('subscription feed includes event urls', function () {
    $user = User::factory()->create();
    $token = $user->generateSubscriptionToken();

    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303',
        'aeat_url' => 'https://sede.agenciatributaria.gob.es/modelo303',
        'applicable_to' => ['autonomo'],
        'year' => now()->year,
    ]);

    $user->favoriteTaxModels()->attach($taxModel);

    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(25),
        'year' => now()->year,
    ]);

    $response = $this->get(route('calendar.subscription.feed', ['token' => $token]));

    $response->assertSuccessful();

    $content = $response->streamedContent();
    expect($content)->toContain('URL');
});

test('subscription feed includes current and next year deadlines', function () {
    $user = User::factory()->create();
    $token = $user->generateSubscriptionToken();

    $taxModel = TaxModel::factory()->create([
        'model_number' => '303',
        'name' => 'Modelo 303',
        'applicable_to' => ['autonomo'],
        'year' => now()->year,
    ]);

    $user->favoriteTaxModels()->attach($taxModel);

    // Current year deadline
    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addDays(30),
        'year' => now()->year,
    ]);

    // Next year deadline
    Deadline::factory()->create([
        'tax_model_id' => $taxModel->id,
        'deadline_date' => now()->addYear()->addDays(30),
        'year' => now()->year + 1,
    ]);

    $response = $this->get(route('calendar.subscription.feed', ['token' => $token]));

    $response->assertSuccessful();
});

test('calendar subscription livewire component loads', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('settings.calendar-subscription')
        ->assertSuccessful();
});

test('calendar subscription component generates token', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    expect($user->subscription_token)->toBeNull();

    Livewire::test('settings.calendar-subscription')
        ->call('generateToken')
        ->assertDispatched('subscription-token-generated');

    expect($user->fresh()->subscription_token)->not->toBeNull();
});

test('calendar subscription component regenerates token', function () {
    $user = User::factory()->create();
    $user->generateSubscriptionToken();
    $originalToken = $user->subscription_token;

    $this->actingAs($user);

    Livewire::test('settings.calendar-subscription')
        ->call('regenerateToken')
        ->assertDispatched('subscription-token-regenerated');

    expect($user->fresh()->subscription_token)->not->toBe($originalToken);
});

test('calendar subscription component displays subscription url', function () {
    $user = User::factory()->create();
    $user->generateSubscriptionToken();

    $this->actingAs($user);

    Livewire::test('settings.calendar-subscription')
        ->assertSet('subscriptionUrl', $user->subscriptionUrl());
});

test('calendar subscription settings page is accessible', function () {
    $user = User::factory()->create(['email_verified_at' => now()]);

    $response = $this->actingAs($user)->get(route('calendar-subscription.edit'));

    $response->assertSuccessful();
});
