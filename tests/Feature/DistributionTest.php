<?php

use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->user = User::factory()->create(['is_admin' => false]);
});

describe('Alumni Distribution', function () {
    it('redirects non-admin users when accessing distribution', function () {
        $this->actingAs($this->user)
            ->get(route('alumni.distribution'))
            ->assertRedirect();
    });

    it('allows admin to view distribution', function () {
        $this->actingAs($this->admin)
            ->get(route('alumni.distribution'))
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('alumni/Distribution')
                    ->has('stateDistribution')
            );
    });
});

describe('Alumni Executives', function () {
    it('redirects non-admin users when accessing executives', function () {
        $this->actingAs($this->user)
            ->get(route('alumni.executives'))
            ->assertRedirect();
    });

    it('allows admin to view executives', function () {
        $this->actingAs($this->admin)
            ->get(route('alumni.executives'))
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('alumni/Executives')
            );
    });
});
