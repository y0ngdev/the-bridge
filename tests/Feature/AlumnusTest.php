<?php

use App\Models\Alumnus;
use App\Models\Department;
use App\Models\Tenure;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->user = User::factory()->create(['is_admin' => false]);
    $this->tenure = Tenure::factory()->create();
    $this->department = Department::factory()->create();
});

describe('Alumni Index', function () {
    it('allows authenticated users to view alumni list', function () {
        $this->actingAs($this->user)
            ->get(route('alumni.index'))
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('alumni/Index')
                    ->has('alumni')
            );
    });

    it('filters alumni by search term', function () {
        Alumnus::factory()->create(['name' => 'John Doe']);
        Alumnus::factory()->create(['name' => 'Jane Smith']);

        $this->actingAs($this->user)
            ->get(route('alumni.index', ['search' => 'John']))
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->has('alumni.data', 1)
            );
    });

    it('filters alumni by tenure', function () {
        $tenure1 = Tenure::factory()->create(['year' => '2020/2021']);
        $tenure2 = Tenure::factory()->create(['year' => '2021/2022']);

        Alumnus::factory()->count(3)->create(['tenure_id' => $tenure1->id]);
        Alumnus::factory()->count(2)->create(['tenure_id' => $tenure2->id]);

        $this->actingAs($this->user)
            ->get(route('alumni.index', ['tenure_id' => $tenure1->id]))
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->has('alumni.data', 3)
            );
    });

    it('searches alumni by phone number', function () {
        Alumnus::factory()->create([
            'name' => 'John Doe',
            'phones' => ['+2341234567890', '+2349876543210'],
        ]);
        Alumnus::factory()->create([
            'name' => 'Jane Smith',
            'phones' => ['+2340987654321'],
        ]);

        $this->actingAs($this->user)
            ->get(route('alumni.index', ['search' => '+2341234567890']))
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->has('alumni.data', 1)
            );
    });
});

describe('Alumni CRUD', function () {
    it('redirects non-admin users when creating alumni', function () {
        $this->actingAs($this->user)
            ->post(route('alumni.store'), [
                'name' => 'Test Alumni',
                'tenure_id' => $this->tenure->id,
            ])
            ->assertRedirect();
    });

    it('allows admin to create alumni', function () {
        $this->actingAs($this->admin)
            ->post(route('alumni.store'), [
                'name' => 'Test Alumni',
                'email' => 'test@example.com',
                'tenure_id' => $this->tenure->id,
                'department_id' => $this->department->id,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('alumni', [
            'name' => 'Test Alumni',
            'email' => 'test@example.com',
        ]);
    });

    it('validates required fields when creating alumni', function () {
        $this->actingAs($this->admin)
            ->post(route('alumni.store'), [])
            ->assertSessionHasErrors(['name', 'tenure_id']);
    });

    it('allows admin to update alumni', function () {
        $alumnus = Alumnus::factory()->create();

        $this->actingAs($this->admin)
            ->put(route('alumni.update', $alumnus), [
                'name' => 'Updated Name',
                'tenure_id' => $this->tenure->id,
            ])
            ->assertRedirect();

        $alumnus->refresh();
        expect($alumnus->name)->toBe('Updated Name');
    });

    it('allows admin to delete alumni', function () {
        $alumnus = Alumnus::factory()->create();

        $this->actingAs($this->admin)
            ->delete(route('alumni.destroy', $alumnus), ['password' => 'password'])
            ->assertRedirect();

        $this->assertDatabaseMissing('alumni', ['id' => $alumnus->id]);
    });
});

describe('Alumni Show', function () {
    it('allows users to view individual alumnus', function () {
        $alumnus = Alumnus::factory()->create(['department_id' => $this->department->id]);

        $this->actingAs($this->user)
            ->get(route('alumni.show', $alumnus))
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('alumni/Show')
                    ->has('alumnus')
            );
    });

    it('loads department relationship', function () {
        $alumnus = Alumnus::factory()->create(['department_id' => $this->department->id]);

        $this->actingAs($this->user)
            ->get(route('alumni.show', $alumnus))
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->has('alumnus.department')
            );
    });
});

describe('Birthdays', function () {
    it('shows birthdays page', function () {
        $this->actingAs($this->user)
            ->get(route('alumni.birthdays'))
            ->assertOk()
            ->assertInertia(
                fn (Assert $page) => $page
                    ->component('alumni/Birthdays')
            );
    });
});

describe('Alumni Export', function () {
    it('allows admin to export alumni', function () {
        Alumnus::factory()->count(5)->create();

        $this->actingAs($this->admin)
            ->get(route('alumni.export'))
            ->assertOk();
    });

    it('redirects non-admin users from export', function () {
        $this->actingAs($this->user)
            ->get(route('alumni.export'))
            ->assertRedirect();
    });

    it('exports with filters applied', function () {
        Alumnus::factory()->count(3)->create(['state' => 'Lagos']);

        $this->actingAs($this->admin)
            ->get(route('alumni.export', ['state' => 'Lagos']))
            ->assertOk();
    });
});

describe('Alumni Import', function () {
    it('redirects non-admin users from import', function () {
        $this->actingAs($this->user)
            ->post(route('alumni.import.store'), [
                'file' => \Illuminate\Http\UploadedFile::fake()->create('alumni.xlsx'),
                'tenure_id' => $this->tenure->id,
            ])
            ->assertRedirect();
    });

    it('validates file is required for import', function () {
        $this->actingAs($this->admin)
            ->post(route('alumni.import.store'), [
                'tenure_id' => $this->tenure->id,
            ])
            ->assertSessionHasErrors(['file']);
    });

    it('validates tenure_id is required for import', function () {
        $this->actingAs($this->admin)
            ->post(route('alumni.import.store'), [
                'file' => \Illuminate\Http\UploadedFile::fake()->create('alumni.xlsx'),
            ])
            ->assertSessionHasErrors(['tenure_id']);
    });
});
