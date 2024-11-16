<?php

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

beforeEach(function () {
    RateLimiter::clear('login:127.0.0.1');
});

test('user can login with valid credentials', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123')
    ]);

    UserProfile::create([
        'user_id' => $user->id
    ]);

    $response = $this->post('/api/login', [
        'email' => $user->email,
        'password' => 'password123'
    ]);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'user' => [
                'id',
                'name',
                'email',
                'created_at',
                'updated_at',
                'user_profile'
            ],
            'token'
        ]);

    $cookie = $response->headers->getCookies()[0];
    expect($cookie->getName())->toBe(env('AUTH_COOKIE_NAME'))
        ->and($cookie->getExpiresTime())->toBeGreaterThan(time());

    // Verify token can be decrypted
    $decryptedToken = Crypt::decryptString($response->json('token'));
    expect($decryptedToken)->toBeString()->not->toBeEmpty();
});

test('login fails with invalid credentials', function () {
    User::factory()->create();

    $response = $this->post('/api/login', [
        'email' => 'test@example.com',
        'password' => 'wrongpassword'
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'The provided credentials do not match our records'
        ]);
});

test('login fails with non-existent email', function () {
    $response = $this->post('/api/login', [
        'email' => 'nonexistent@example.com',
        'password' => 'password123'
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'message' => 'The provided credentials do not match our records'
        ]);
});

test('login requires valid email and password', function () {
    $response = $this->post('/api/login', [
        'email' => 'invalid-email',
        'password' => ''
    ], ['Accept' => 'application/json']);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email', 'password']);
});

test('rate limiting blocks too many attempts', function () {
    User::factory()->create();

    for ($i = 0; $i < 6; $i++) {
        $this->post('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword'
        ]);
    }

    $response = $this->post('/api/login', [
        'email' => 'test@example.com',
        'password' => 'wrongpassword'
    ]);

    $response->assertStatus(429)
        ->assertJson([
            'message' => 'Too many login attempts. Please try again in 1 minute.'
        ]);
});
