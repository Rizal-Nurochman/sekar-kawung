<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('renders the admin login page for guests', function () {
    $this->get(route('admin.login'))
        ->assertOk()
        ->assertSee('Login', false)
        ->assertDontSee('Sekar Kawung', false);
});

it('redirects guests away from the dashboard to login', function () {
    $this->get(route('admin.dashboard'))->assertRedirect('/admin/login');
});

it('redirects authenticated admins away from login to the dashboard', function () {
    $this->seed();
    $user = User::query()->where('email', 'test@example.com')->firstOrFail();

    $this->actingAs($user)
        ->get(route('admin.login'))
        ->assertRedirect(route('admin.dashboard'));
});

it('rejects login with invalid credentials', function () {
    User::factory()->create([
        'email' => 'admin@example.com',
        'password' => 'password123',
    ]);

    $this->post(route('admin.login'), [
        'email' => 'admin@example.com',
        'password' => 'wrong-password',
    ])->assertSessionHasErrors('email');

    $this->assertGuest();
});

it('rejects login with invalid payload', function () {
    $this->post(route('admin.login'), [
        'email' => 'not-an-email',
        'password' => 'short',
    ])->assertSessionHasErrors(['email', 'password']);

    $this->assertGuest();
});

it('logs in with valid credentials and renders the dashboard', function () {
    $this->seed();
    $user = User::query()->where('email', 'test@example.com')->firstOrFail();

    $this->post(route('admin.login'), [
        'email' => 'test@example.com',
        'password' => 'password',
    ])->assertRedirect(route('admin.dashboard'));

    $this->assertAuthenticatedAs($user);
    $this->get(route('admin.dashboard'))->assertOk()->assertSee('Dashboard', false);
});

it('logs out the admin', function () {
    $this->seed();
    $user = User::query()->where('email', 'test@example.com')->firstOrFail();
    $this->actingAs($user);

    $this->post(route('admin.logout'))->assertRedirect(route('admin.login'));

    $this->assertGuest();
});
