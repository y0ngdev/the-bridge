<?php

use App\Models\Department;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->user = User::factory()->create(['is_admin' => false]);
});

describe('Department Index', function () {
    it('redirects non-admin users when accessing departments', function () {
        $this->actingAs($this->user)
            ->get(route('departments.index'))
            ->assertRedirect();
    });

    it('allows admin to view departments', function () {
        $this->actingAs($this->admin)
            ->get(route('departments.index'))
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('departments/Index')
            );
    });
});

describe('Department CRUD', function () {
    it('allows admin to create department', function () {
        $this->actingAs($this->admin)
            ->post(route('departments.store'), [
                'name' => 'Computer Science',
                'code' => 'CSC',
                'school' => 'SAAT',
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('departments', [
            'name' => 'Computer Science',
            'code' => 'CSC',
        ]);
    });

    it('allows admin to update department', function () {
        $department = Department::factory()->create();

        $this->actingAs($this->admin)
            ->put(route('departments.update', $department), [
                'name' => 'Updated Department',
                'code' => 'UPD',
                'school' => 'TEST',
            ])
            ->assertRedirect();

        $department->refresh();
        expect($department->name)->toBe('Updated Department');
    });

    it('allows admin to delete department', function () {
        $department = Department::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('departments.destroy', $department))
            ->assertRedirect();

        $this->assertDatabaseMissing('departments', ['id' => $department->id]);
    });
});

describe('Department Options', function () {
    it('returns id as value in options', function () {
        Department::query()->delete(); // Clear existing
        $department = Department::factory()->create(['name' => 'Test Dept']);

        $options = Department::options();

        expect($options)->toBeArray();
        expect($options[0]['value'])->toBe($department->id);
        expect($options[0]['label'])->toBe('Test Dept');
    });
});
