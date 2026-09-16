<?php

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('requires authentication and protects administrator resources by role', function () {
    $this->getJson('/api/v1/users')->assertUnauthorized();

    $registrar = User::factory()->registrar()->create();
    $this->actingAs($registrar)->getJson('/api/v1/users')->assertForbidden();

    $administrator = User::factory()->administrator()->create();
    $this->actingAs($administrator)->getJson('/api/v1/users')->assertOk();
});

it('isolates student self service records', function () {
    $firstUser = User::factory()->student()->create();
    $secondUser = User::factory()->student()->create();
    $first = Student::factory()->create(['user_id' => $firstUser->id]);
    $second = Student::factory()->create(['user_id' => $secondUser->id]);

    $this->actingAs($firstUser)->getJson('/api/v1/me/student')->assertOk()->assertJsonPath('data.id', $first->id);
    $this->getJson("/api/v1/students/{$second->id}")->assertForbidden();
    $this->getJson('/api/v1/me/academic-record')->assertOk()->assertJsonPath('data.student.id', $first->id);
});

it('does not allow suspended sessions to access protected endpoints', function () {
    $user = User::factory()->administrator()->create(['status' => 'suspended']);

    $this->actingAs($user)->getJson('/api/v1/dashboard')->assertForbidden();
});
