<?php

use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

describe('Backup', function () {
    it('shows backup page', function () {
        $this->actingAs($this->user)
            ->get(route('backup.index'))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page
                    ->component('settings/Backup')
            );
    });
});

describe('Calendar Settings', function () {
    it('shows calendar settings page', function () {
        $this->actingAs($this->user)
            ->get(route('calendar.index'))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page
                    ->component('settings/Calendar')
            );
    });
});
