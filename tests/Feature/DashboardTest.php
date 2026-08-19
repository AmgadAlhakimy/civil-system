<?php

use App\Models\User;

test('guests are redirected to the Filament login page', function () {
    $response = $this->get('/dashboard');

    $response->assertRedirect('/dashboard/login');
});

test('authenticated users can visit the dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
});
