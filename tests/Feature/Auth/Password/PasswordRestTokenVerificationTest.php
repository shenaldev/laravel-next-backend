<?php

use App\Models\PasswordReset;
use App\Models\User;

test('verify reset token is valid', function () {
    $user = User::factory()->create();
    $token = 'valid-token';

    PasswordReset::create([
        'email' => $user->email,
        'token' => $token,
        'created_at' => now(),
    ]);

    $this->postJson('api/forgot-password-verify', [
        'email' => $user->email,
        'token' => $token,
    ])->assertStatus(200)
        ->assertJson(['message' => 'success', 'error' => false]);
});

test('fails to verify reset token with invalid token', function () {
    $user = User::factory()->create();
    $token = 'valid-token';

    PasswordReset::create([
        'email' => $user->email,
        'token' => $token,
        'created_at' => now(),
    ]);

    $this->postJson('api/forgot-password-verify', [
        'email' => $user->email,
        'token' => 'invalid-token',
    ])->assertStatus(422)
        ->assertJson(['message' => 'Invalid reset token']);
});

test('require email and token to verify reset token', function () {
    $this->postJson('api/forgot-password-verify')
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'token']);
});

test('fails to verify reset token if token is expired', function () {
    $user = User::factory()->create();
    $token = 'valid-token';

    PasswordReset::create([
        'email' => $user->email,
        'token' => $token,
        'created_at' => now()->subMinutes(20),
    ]);

    $this->postJson('api/forgot-password-verify', [
        'email' => $user->email,
        'token' => $token,
    ])->assertStatus(422)
        ->assertJson(['message' => 'The reset token has expired. Please request a new token.']);
});

test('return error if reset token not in the database', function () {
    $user = User::factory()->create();

    $this->postJson('api/forgot-password-verify', [
        'email' => $user->email,
        'token' => 'test-token',
    ])->assertStatus(422)
        ->assertJson(['message' => 'Invalid token. Please request a new token.']);
});
