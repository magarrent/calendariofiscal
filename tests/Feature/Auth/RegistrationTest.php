<?php

use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register with valid data', function () {
    $response = $this->from(route('register'))->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'terms' => true,
        'privacy' => true,
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();

    $user = User::where('email', 'test@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('John Doe')
        ->and($user->email)->toBe('test@example.com')
        ->and($user->terms_accepted)->toBeTrue()
        ->and($user->privacy_accepted)->toBeTrue()
        ->and($user->terms_accepted_at)->not->toBeNull()
        ->and($user->privacy_accepted_at)->not->toBeNull()
        ->and($user->email_verified_at)->toBeNull()
        ->and(Hash::check('Password123!', $user->password))->toBeTrue();
});

test('registration requires name', function () {
    $response = $this->post(route('register.store'), [
        'name' => '',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'terms' => true,
        'privacy' => true,
    ]);

    $response->assertSessionHasErrors('name');
    $this->assertGuest();
});

test('registration requires email', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => '',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'terms' => true,
        'privacy' => true,
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('registration requires valid email', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'invalid-email',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'terms' => true,
        'privacy' => true,
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('registration requires unique email', function () {
    User::factory()->create(['email' => 'test@example.com']);

    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'terms' => true,
        'privacy' => true,
    ]);

    $response->assertSessionHasErrors('email');
    $this->assertGuest();
});

test('registration requires password', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => '',
        'password_confirmation' => '',
        'terms' => true,
        'privacy' => true,
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

test('registration requires password confirmation', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'DifferentPassword123!',
        'terms' => true,
        'privacy' => true,
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

test('registration enforces minimum password length', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'Pass1!',
        'password_confirmation' => 'Pass1!',
        'terms' => true,
        'privacy' => true,
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

test('registration enforces password must contain letters', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => '12345678!',
        'password_confirmation' => '12345678!',
        'terms' => true,
        'privacy' => true,
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

test('registration enforces password must contain mixed case', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password123!',
        'password_confirmation' => 'password123!',
        'terms' => true,
        'privacy' => true,
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

test('registration enforces password must contain numbers', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'Password!',
        'password_confirmation' => 'Password!',
        'terms' => true,
        'privacy' => true,
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

test('registration enforces password must contain symbols', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'Password123',
        'password_confirmation' => 'Password123',
        'terms' => true,
        'privacy' => true,
    ]);

    $response->assertSessionHasErrors('password');
    $this->assertGuest();
});

test('registration requires terms acceptance', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'privacy' => true,
    ]);

    $response->assertSessionHasErrors('terms');
    $this->assertGuest();
});

test('registration requires privacy acceptance', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'terms' => true,
    ]);

    $response->assertSessionHasErrors('privacy');
    $this->assertGuest();
});

test('registration requires both terms and privacy acceptance', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
    ]);

    $response->assertSessionHasErrors(['terms', 'privacy']);
    $this->assertGuest();
});

test('registration records terms acceptance timestamp', function () {
    $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'terms' => true,
        'privacy' => true,
    ]);

    $user = User::where('email', 'test@example.com')->first();
    expect($user->terms_accepted_at)->not->toBeNull()
        ->and($user->terms_accepted_at->isToday())->toBeTrue();
});

test('registration records privacy acceptance timestamp', function () {
    $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'terms' => true,
        'privacy' => true,
    ]);

    $user = User::where('email', 'test@example.com')->first();
    expect($user->privacy_accepted_at)->not->toBeNull()
        ->and($user->privacy_accepted_at->isToday())->toBeTrue();
});

test('new users require email verification', function () {
    $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'terms' => true,
        'privacy' => true,
    ]);

    $user = User::where('email', 'test@example.com')->first();
    expect($user->email_verified_at)->toBeNull()
        ->and($user->hasVerifiedEmail())->toBeFalse();
});
