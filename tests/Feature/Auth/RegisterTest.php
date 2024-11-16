<?php

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Crypt;

test('user can register with valid data', function () {
    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123'
    ];

    $response = $this->post('/api/register', $userData, ['Accept' => 'application/json']);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'user' => [
                'id',
                'name',
                'email',
                'email_verified_at',
                'created_at',
                'updated_at',
                'user_profile' => [
                    'id',
                    'user_id',
                    'created_at',
                    'updated_at'
                ]
            ],
            'token'
        ]);

    // Assert User Created
    $user = User::where('email', 'john@example.com')->first();
    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('John Doe')
        ->and(Hash::check('password123', $user->password))->toBeTrue()
        ->and($user->email_verified_at)->not->toBeNull();

    // Assert UserProfile Created
    $userProfile = UserProfile::where('user_id', $user->id)->first();
    expect($userProfile)->not->toBeNull();

    // Assert Cookie
    $cookie = $response->headers->getCookies()[0];
    expect($cookie->getName())->toBe(env('AUTH_COOKIE_NAME'))
        ->and($cookie->getExpiresTime())->toBeGreaterThan(time());

    // Verify token can be decrypted
    $decryptedToken = Crypt::decryptString($response->json('token'));
    expect($decryptedToken)->toBeString()->not->toBeEmpty();
});

test('registration fails with existing email', function () {
    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123'
    ];

    User::create($userData);

    $response = $this->post('/api/register', $userData, ['Accept' => 'application/json']);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('registration requires valid name', function () {
    $response = $this->post('/api/register', [
        'name' => null,
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123'
    ], ['Accept' => 'application/json']);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);

    $response = $this->post('/api/register', [
        'name' => str_repeat('a', 201),
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123'
    ], ['Accept' => 'application/json']);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});

test('registration requires valid email', function () {
    $response = $this->post('/api/register', [
        'name' => 'John Doe',
        'email' => 'invalid-email',
        'password' => 'password123',
        'password_confirmation' => 'password123'
    ], ['Accept' => 'application/json']);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('registration requires valid password and password confirmation', function () {
    $response = $this->post('/api/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'pass',
        'password_confirmation' => 'pass'
    ], ['Accept' => 'application/json']);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);

    // Test password confirmation mismatch
    $response = $this->post('/api/register', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different123'
    ], ['Accept' => 'application/json']);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['password']);
});
