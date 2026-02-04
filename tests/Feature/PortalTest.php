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

test('portal lookup handles multiple matches', function () {
    $tenure = Tenure::factory()->create();
    // Create two similar alumni
    $a1 = Alumnus::factory()->create(['name' => 'John A. Doe', 'tenure_id' => $tenure->id]);
    $a2 = Alumnus::factory()->create(['name' => 'John B. Doe', 'tenure_id' => $tenure->id]);

    $response = $this->post('/portal/lookup', [
        'name' => 'John', // Partial match for both
    ]);

    $matches = session('possible_matches');
    expect($matches)->toHaveCount(2);
    expect($matches->pluck('id'))->toContain($a1->id);
    expect($matches->pluck('id'))->toContain($a2->id);
});

test('portal lookup works with name only', function () {
    $tenure = Tenure::factory()->create();
    $alumnus = Alumnus::factory()->create(['name' => 'Name Only Target']);

    $response = $this->post('/portal/lookup', [
        'name' => 'Name Only Target',
        // 'email' and 'phone' omitted
    ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();
    $response->assertSessionHas('match');
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

test('admin can approve pending update', function () {
    $user = \App\Models\User::factory()->create(['is_admin' => true]);
    $tenure = Tenure::factory()->create();
    $alumnus = Alumnus::factory()->create([
        'tenure_id' => $tenure->id,
        'occupation' => 'Old Job',
        'is_futa_staff' => false,
    ]);

    $update = \App\Models\PendingAlumnusUpdate::create([
        'alumnus_id' => $alumnus->id,
        'changes' => [
            'occupation' => 'New Job',
            'is_futa_staff' => true,
        ],
        'status' => 'pending',
    ]);

    // $update->refresh();
    // dump('Update Changes:', $update->changes);

    $response = $this->actingAs($user)
        ->post(route('admin.pending-updates.approve', $update));

    $response->assertRedirect();
    $response->assertSessionHas('success');

    $alumnus->refresh();
    $update->refresh();

    $this->assertDatabaseHas('alumni', [
        'id' => $alumnus->id,
        'occupation' => 'New Job',
    ]);

    $this->assertDatabaseHas('alumni', [
        'id' => $alumnus->id,
        'is_futa_staff' => true,
    ]);

    expect($update->status)->toBe('approved');
    expect($update->reviewed_by)->toBe($user->id);
});
