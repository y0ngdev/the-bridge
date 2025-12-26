<?php

use App\Models\Tenure;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->user = User::factory()->create(['is_admin' => false]);
});

describe('Tenure Index', function () {
    it('redirects non-admin users when accessing tenures', function () {
        $this->actingAs($this->user)
            ->get(route('tenures.index'))
            ->assertRedirect();
    });

    it('allows admin to view tenures', function () {
        $this->actingAs($this->admin)
            ->get(route('tenures.index'))
            ->assertOk()
            ->assertInertia(
                fn(Assert $page) => $page
                    ->component('tenures/Index')
                    ->has('tenures')
            );
    });
});

describe('Tenure CRUD', function () {
    it('allows admin to create tenure', function () {
        $this->actingAs($this->admin)
            ->post(route('tenures.store'), [
                'year' => '2024/2025',
                'name' => 'Session 2024',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('tenures', ['year' => '2024/2025']);
    });

    it('validates tenure year is required', function () {
        $this->actingAs($this->admin)
            ->post(route('tenures.store'), [])
            ->assertSessionHasErrors(['year']);
    });

    it('allows admin to update tenure', function () {
        $tenure = Tenure::factory()->create(['year' => '2020/2021']);

        $this->actingAs($this->admin)
            ->put(route('tenures.update', $tenure), [
                'year' => '2021/2022',
                'name' => 'Updated Session',
            ])
            ->assertRedirect();

        $tenure->refresh();
        expect($tenure->year)->toBe('2021/2022');
    });
});
