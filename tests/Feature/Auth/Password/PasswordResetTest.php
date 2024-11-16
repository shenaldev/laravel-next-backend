<?php

use App\Models\PasswordReset;
use App\Models\User;

test('user can reset password with valid token', function () {
    $user = User::factory()->create();

    PasswordReset::create([
        'email' => $user->email,
        'token' => 'valid_token',
        'created_at' => now(),
    ]);

    $this->postJson('/api/reset-password', [
        'email' => $user->email,
        'token' => 'valid_token',
        'password' => 'new_password',
        'password_confirmation' => 'new_password',
    ])->assertOk();
});

test('user cannot reset password with invalid token', function () {
    $user = User::factory()->create();

    PasswordReset::create([
        'email' => $user->email,
        'token' => 'valid_token',
        'created_at' => now(),
    ]);

    $this->postJson('/api/reset-password', [
        'email' => $user->email,
        'token' => 'invalid_token',
        'password' => 'new_password',
        'password_confirmation' => 'new_password',
    ])->assertStatus(422)
        ->assertJson(['message' => 'Invalid reset token']);
});

test('user cannot reset password with expired token', function () {
    $user = User::factory()->create();

    PasswordReset::create([
        'email' => $user->email,
        'token' => 'valid_token',
        'created_at' => now()->subMinutes(30),
    ]);

    $this->postJson('/api/reset-password', [
        'email' => $user->email,
        'token' => 'valid_token',
        'password' => 'new_password',
        'password_confirmation' => 'new_password',
    ])->assertStatus(422)
        ->assertJson(['message' => 'The reset token has expired. Please request a new token.']);
});

test('user needs to provide email, token, password, and password confirmation to reset password', function () {
    $this->postJson('/api/reset-password')
        ->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'token', 'password']);
});
