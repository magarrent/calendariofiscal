<?php

use App\Models\TaxModel;
use App\Models\User;
use App\Models\UserDeadline;
use App\Models\UserModelCompletion;
use App\Models\UserModelNote;

test('user can mark tax model as favorite', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create();

    $user->toggleFavorite($taxModel);

    expect($user->hasFavorite($taxModel))->toBeTrue();
    expect($user->favoriteTaxModels)->toHaveCount(1);
});

test('user can unfavorite a tax model', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create();

    $user->toggleFavorite($taxModel);
    expect($user->hasFavorite($taxModel))->toBeTrue();

    $user->toggleFavorite($taxModel);
    expect($user->hasFavorite($taxModel))->toBeFalse();
});

test('user can create personal deadline', function () {
    $user = User::factory()->create();

    $deadline = UserDeadline::create([
        'user_id' => $user->id,
        'title' => 'Test Deadline',
        'description' => 'Test Description',
        'deadline_date' => now()->addDays(7),
        'year' => now()->year,
    ]);

    expect($user->deadlines)->toHaveCount(1);
    expect($deadline->title)->toBe('Test Deadline');
});

test('user can add note to tax model', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create();

    $note = UserModelNote::create([
        'user_id' => $user->id,
        'tax_model_id' => $taxModel->id,
        'note' => 'Test note',
        'filing_number' => 'REF-123',
    ]);

    expect($note->note)->toBe('Test note');
    expect($note->filing_number)->toBe('REF-123');
    expect($note->user_id)->toBe($user->id);
});

test('user can mark tax model as completed for a year', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create();
    $year = now()->year;

    $user->toggleModelCompletion($taxModel, $year);

    expect($user->hasCompletedModel($taxModel, $year))->toBeTrue();
});

test('user can unmark tax model completion', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create();
    $year = now()->year;

    $user->toggleModelCompletion($taxModel, $year);
    expect($user->hasCompletedModel($taxModel, $year))->toBeTrue();

    $user->toggleModelCompletion($taxModel, $year);
    expect($user->hasCompletedModel($taxModel, $year))->toBeFalse();
});

test('user model completion tracks completion date', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create();
    $year = now()->year;

    $user->toggleModelCompletion($taxModel, $year);

    $completion = UserModelCompletion::where('user_id', $user->id)
        ->where('tax_model_id', $taxModel->id)
        ->where('year', $year)
        ->first();

    expect($completion->completed)->toBeTrue();
    expect($completion->completed_at)->not->toBeNull();
});

test('user can have multiple deadlines', function () {
    $user = User::factory()->create();

    UserDeadline::create([
        'user_id' => $user->id,
        'title' => 'Deadline 1',
        'deadline_date' => now()->addDays(7),
        'year' => now()->year,
    ]);

    UserDeadline::create([
        'user_id' => $user->id,
        'title' => 'Deadline 2',
        'deadline_date' => now()->addDays(14),
        'year' => now()->year,
    ]);

    expect($user->deadlines)->toHaveCount(2);
});

test('user can have notes for multiple tax models', function () {
    $user = User::factory()->create();
    $taxModel1 = TaxModel::factory()->create();
    $taxModel2 = TaxModel::factory()->create();

    UserModelNote::create([
        'user_id' => $user->id,
        'tax_model_id' => $taxModel1->id,
        'note' => 'Note for model 1',
    ]);

    UserModelNote::create([
        'user_id' => $user->id,
        'tax_model_id' => $taxModel2->id,
        'note' => 'Note for model 2',
    ]);

    $notes = UserModelNote::where('user_id', $user->id)->get();
    expect($notes)->toHaveCount(2);
});

test('user note is unique per tax model', function () {
    $user = User::factory()->create();
    $taxModel = TaxModel::factory()->create();

    UserModelNote::create([
        'user_id' => $user->id,
        'tax_model_id' => $taxModel->id,
        'note' => 'First note',
    ]);

    expect(function () use ($user, $taxModel) {
        UserModelNote::create([
            'user_id' => $user->id,
            'tax_model_id' => $taxModel->id,
            'note' => 'Second note',
        ]);
    })->toThrow(\Illuminate\Database\UniqueConstraintViolationException::class);
});
