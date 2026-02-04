<?php

use App\Models\Alumnus;
use App\Models\Tenure;
use App\Models\Department;
use App\Enums\PastExcoOffice;
use App\Enums\NigerianState;
use App\Enums\Unit;

test('portal page can be rendered with all props', function () {
    $response = $this->get('/portal');
    $response->assertStatus(200);
    $response->assertInertia(
        fn($page) => $page
            ->component('public/Portal')
            ->has('tenures')
            ->has('departments')
            ->has('states')
            ->has('units')
            ->has('pastExcoOffices')
    );
});

test('portal lookup finds matching alumnus', function () {
    $tenure = Tenure::factory()->create();
    $alumnus = Alumnus::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'tenure_id' => $tenure->id,
    ]);

    $response = $this->post('/portal/lookup', [
        'name' => 'John Doe',
        'email' => 'john@example.com',
    ]);

    $response->assertSessionHas('match');
    expect(session('match')->id)->toBe($alumnus->id);
});

test('portal can create new alumnus with all fields', function () {
    $tenure = Tenure::factory()->create();
    $department = Department::factory()->create();
    // Use first case for Enums to ensure validity
    $state = NigerianState::cases()[0]->value;
    $unit = Unit::cases()[0]->value;
    $office = PastExcoOffice::cases()[0]->value;

    $data = [
        'name' => 'New User',
        'email' => 'new@example.com',
        'phones' => ['08012345678'],
        'tenure_id' => $tenure->id,
        'department_id' => $department->id,
        'address' => '123 Street',
        'current_employer' => 'Tech Corp',
        'state' => $state,
        'unit' => $unit,
        'gender' => 'M',
        'birth_date' => '1990-01-01',
        'past_exco_office' => $office,
        'current_exco_office' => 'Advisor',
        'is_futa_staff' => true,
        'marital_status' => 'Single',
        'occupation' => 'Developer',
    ];

    $response = $this->post('/portal/submit', $data);

    $response->assertSessionHasNoErrors();
    $response->assertSessionHas('success');
    $this->assertDatabaseHas('alumni', [
        'name' => 'New User',
        'email' => 'new@example.com',
        'address' => '123 Street',
        'is_futa_staff' => true,
        'past_exco_office' => $office,
        'occupation' => 'Developer',
    ]);
});

test('portal update request submits pending update', function () {
    $tenure = Tenure::factory()->create();
    $alumnus = Alumnus::factory()->create([
        'tenure_id' => $tenure->id,
        'is_futa_staff' => false,
    ]);

    $data = [
        'name' => $alumnus->name,
        'email' => $alumnus->email,
        'phones' => $alumnus->phones ?? [],
        'tenure_id' => $tenure->id,
        'is_futa_staff' => true, // Changed
        'occupation' => 'Developer', // Changed to check new field
    ];

    $response = $this->post("/portal/update/{$alumnus->id}", $data);

    if (session('info')) {
        dump('Info session present:', session('info'));
    }
    if (session('errors')) {
        dump('Session errors:', session('errors')->all());
    }

    $response->assertSessionHasNoErrors();
    $response->assertSessionHas('success');

    // Check that the pending update was created
    $this->assertDatabaseHas('pending_alumni_updates', [
        'alumnus_id' => $alumnus->id,
    ]);

    // Verify specific changes are recorded
    $update = \App\Models\PendingAlumnusUpdate::where('alumnus_id', $alumnus->id)->latest()->first();

    // Debug output if update is null
    if (!$update) {
        dump('Pending update not found in DB.');
    }

    // Cast to array or access as object depending on model casting
    $changes = $update->changes;

    expect($changes)->toHaveKey('is_futa_staff', true);
    expect($changes)->toHaveKey('occupation', 'Developer');
});
