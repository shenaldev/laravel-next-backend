<?php

use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\PasswordResetMail;


test('a user can request a password reset mail', function () {
    Mail::fake();

    $user = User::factory()->create();

    $this->post('/api/forgot-password', ['email' => $user->email])
        ->assertStatus(200)
        ->assertJson(['message' => 'Success', 'error' => false]);

    Mail::assertSent(PasswordResetMail::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});

test('need to provide email vaild to request a password reset mail', function () {
    $this->postJson('/api/forgot-password')
        ->assertStatus(422)
        ->assertJsonValidationErrors('email');
});

test('email should be in the database to request a reset mail', function () {
    $this->postJson('/api/forgot-password', ['email' => 'test@mail.com'])
        ->assertStatus(422)
        ->assertJson(['message' => 'Email address not found in our records.']);
});

test('returns error if fails to send email', function () {
    Mail::fake();
    Mail::shouldReceive('to')->andThrow(new Exception('Failed to send email'));

    $user = User::factory()->create();

    $this->postJson('/api/forgot-password', ['email' => $user->email])
        ->assertStatus(500)
        ->assertJson(['message' => "Failed to send email", 'error' => true]);
});
