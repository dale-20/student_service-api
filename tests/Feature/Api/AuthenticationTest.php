<?php

use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

uses(RefreshDatabase::class);

it('logs an active user in without exposing secrets and logs them out', function () {
    $user = User::factory()->administrator()->create(['password' => 'ValidPassword!23']);

    $response = $this->withHeader('Origin', 'http://localhost:3000')->postJson('/api/v1/auth/login', ['email' => $user->email, 'password' => 'ValidPassword!23']);

    $response->assertOk()->assertJsonPath('data.id', $user->id)->assertJsonMissingPath('data.password');
    $this->assertAuthenticatedAs($user);
    expect($user->fresh()->last_login_at)->not->toBeNull();

    $this->withHeader('Origin', 'http://localhost:3000')->postJson('/api/v1/auth/logout')->assertNoContent();
    $this->assertFalse(session()->has(auth()->guard('web')->getName()));
});

it('uses the same generic response for invalid and inactive accounts', function () {
    $inactive = User::factory()->create(['status' => UserStatus::Inactive, 'password' => 'ValidPassword!23']);

    $this->postJson('/api/v1/auth/login', ['email' => $inactive->email, 'password' => 'ValidPassword!23'])
        ->assertUnprocessable()->assertJsonPath('errors.email.0', 'The provided credentials are incorrect.');
    $this->postJson('/api/v1/auth/login', ['email' => 'missing@example.test', 'password' => 'ValidPassword!23'])
        ->assertUnprocessable()->assertJsonPath('errors.email.0', 'The provided credentials are incorrect.');
});

it('forces provisioned users to replace their initial password', function () {
    $user = User::factory()->administrator()->create(['must_change_password' => true, 'password' => 'InitialPassword!23']);

    $this->actingAs($user)->getJson('/api/v1/dashboard')->assertForbidden();
    $this->withHeader('Origin', 'http://localhost:3000')->putJson('/api/v1/auth/password', [
        'current_password' => 'InitialPassword!23',
        'password' => 'ReplacementPassword!45',
        'password_confirmation' => 'ReplacementPassword!45',
    ])->assertOk()->assertJsonPath('data.must_change_password', false);

    expect(Hash::check('ReplacementPassword!45', $user->fresh()->password))->toBeTrue();
    $this->getJson('/api/v1/dashboard')->assertOk();
});

it('rate limits repeated login attempts', function () {
    RateLimiter::clear('login');
    $payload = ['email' => 'limited@example.test', 'password' => 'WrongPassword!23'];

    foreach (range(1, 5) as $attempt) {
        $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.55'])->postJson('/api/v1/auth/login', $payload)->assertUnprocessable();
    }

    $this->withServerVariables(['REMOTE_ADDR' => '192.0.2.55'])->postJson('/api/v1/auth/login', $payload)->assertTooManyRequests();
});
