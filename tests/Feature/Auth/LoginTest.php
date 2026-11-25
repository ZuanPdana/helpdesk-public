<?php

use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutVite();
});

test('admin login redirects to the admin dashboard', function () {
    $admin = User::factory()->admin()->create(['password' => 'password']);

    $this->post(route('login'), [
        'email' => $admin->email,
        'password' => 'password',
    ])
        ->assertRedirect(route('admin.dashboard'));
});

test('technician login redirects to the technician dashboard', function () {
    $technician = User::factory()->technician()->create(['password' => 'password']);

    $this->post(route('login'), [
        'email' => $technician->email,
        'password' => 'password',
    ])
        ->assertRedirect(route('technician.dashboard'));
});

test('user login redirects to the user dashboard', function () {
    $user = User::factory()->user()->create(['password' => 'password']);

    $this->post(route('login'), [
        'email' => $user->email,
        'password' => 'password',
    ])
        ->assertRedirect(route('user.dashboard'));
});

test('authenticated admin visiting the root is sent to the admin dashboard', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/')
        ->assertRedirect(route('admin.dashboard'));
});
