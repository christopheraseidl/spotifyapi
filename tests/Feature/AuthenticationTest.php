<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can register a new user', function () {
    $email = 'test@example.com';
    $userData = [
        'name' => 'Test User',
        'email' => $email,
        'password' => 'password123',
    ];

    $response = $this->withheaders([
        'Accept' => 'application/json',
    ])->post('/api/register', $userData);
    $user = User::firstWhere('email', $email);

    expect($response->getStatusCode())->toBe(201)
        ->and($user)->toBeInstanceOf(User::class)
        ->and($user->email)->toBe($email);
});

it('cannot register with invalid credentials', function () {
    $email = 'not_email';
    $userData = [
        'email' => $email,
        'password' => 'short',
    ];

    $response = $this->withheaders([
        'Accept' => 'application/json',
    ])->post('/api/register', $userData);
    $errors = $response->exception->errors();
    $user = User::firstWhere('email', $email);

    expect($errors['name'][0])->toBe('The name field is required.')
        ->and($errors['email'][0])->toBe('The email field must be a valid email address.')
        ->and($errors['password'][0])->toBe('The password field must be at least 8 characters.')
        ->and($user)->toBeNull();
});

it('cannot register with duplicate email', function () {
    $email = 'test@example.com';
    $userData = [
        'name' => 'Test User',
        'email' => $email,
        'password' => 'password123',
    ];
    User::factory()->create($userData);

    $response = $this->withheaders([
        'Accept' => 'application/json',
    ])->post('/api/register', $userData);
    $errors = $response->exception->errors();
    $user = User::firstWhere('email', $email);

    expect($errors['email'][0])->toBe('The email has already been taken.');
});

it('can login with the correct credentials', function () {
    $email = 'test@example.com';
    $userData = [
        'name' => 'Test User',
        'email' => $email,
        'password' => 'password123',
    ];
    User::factory()->create($userData);

    $response = $this->withheaders([
        'Accept' => 'application/json',
    ])->post('/api/login', $userData);

    expect($response->getStatusCode())->toBe(200)
        ->and($response->json())
        ->message
        ->toBe('Authenticated.');
});

it('cannot login with the incorrect credentials', function () {
    $email = 'test@example.com';
    $userData = [
        'name' => 'Test User',
        'email' => $email,
        'password' => 'password123',
    ];
    $user = User::factory()->create($userData);

    $userData['password'] = 'wrong';

    $response = $this->withheaders([
        'Accept' => 'application/json',
    ])->post('/api/login', $userData);

    expect($response->getStatusCode())->toBe(401)
        ->and($response->json())
        ->message
        ->toBe('Invalid credentials.');
});

it('can logout when authenticated', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeaders([
        'Authorization' => 'Bearer '.$token,
    ])->post('/api/logout');

    expect($response->getStatusCode())->toBe(200);
});

it('cannot logout when not authenticated', function () {
    $response = $this->withheaders([
        'Accept' => 'application/json',
    ])->post('/api/logout');

    expect($response->getStatusCode())->toBe(401);
});

it('can access protected routes when authenticated', function () {
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;

    $response = $this->withHeaders([
        'Accept' => 'application/json',
        'Authorization' => 'Bearer '.$token,
    ])->get('api/user');

    expect($response->getStatusCode())->toBe(200);
});

it('cannot access protected routes when not authenticated', function () {
    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->get('api/user');

    expect($response->getStatusCode())->toBe(401);
});
