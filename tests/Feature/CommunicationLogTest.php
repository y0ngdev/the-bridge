<?php

use App\Models\Alumnus;
use App\Models\User;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->user = User::factory()->create(['is_admin' => false]);
    $this->alumnus = Alumnus::factory()->create();
});

describe('Communication Logs', function () {
    it('allows users to create communication logs', function () {
        $this->actingAs($this->user)
            ->post(route('alumni.communications.store', $this->alumnus), [
                'type' => 'call',
                'outcome' => 'successful',
                'notes' => 'Test communication',
                'occurred_at' => now()->toDateTimeString(),
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('communication_logs', [
            'alumnus_id' => $this->alumnus->id,
            'type' => 'call',
            'notes' => 'Test communication',
        ]);
    });

    it('validates communication log type', function () {
        $this->actingAs($this->user)
            ->post(route('alumni.communications.store', $this->alumnus), [])
            ->assertSessionHasErrors(['type', 'outcome', 'occurred_at']);
    });
});
