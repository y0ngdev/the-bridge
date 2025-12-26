<?php

use App\Models\Tenure;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->user = User::factory()->create(['is_admin' => false]);
});

describe('Outreach Analytics', function () {
    it('redirects non-admin users when accessing outreach', function () {
        $this->actingAs($this->user)
            ->get(route('analytics.outreach'))
            ->assertRedirect();
    });

    it('allows admin to view outreach analytics', function () {
        Tenure::factory()->create(['is_active' => true]);

        $this->actingAs($this->admin)
            ->get(route('analytics.outreach'))
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('analytics/Outreach')
                    ->has('stats')
            );
    });

    it('shows active session in response', function () {
        $tenure = Tenure::factory()->create(['is_active' => true]);

        $this->actingAs($this->admin)
            ->get(route('analytics.outreach'))
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->has('active_session')
            );
    });
});
