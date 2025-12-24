<?php

use App\Models\Alumnus;
use App\Models\Tenure;
use App\Models\User;

test('dashboard is accessible to authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertStatus(200);
});

test('dashboard displays stats correctly', function () {
    $user = User::factory()->create();
    $tenure = Tenure::factory()->create(['year' => '2023/2024']);
    Alumnus::factory()->count(3)->create(['tenure_id' => $tenure->id]);

    $response = $this->actingAs($user)->get('/dashboard');

    $response->assertInertia(
        fn ($page) => $page
            ->component('Dashboard')
            ->missing('stats')
    );
});
