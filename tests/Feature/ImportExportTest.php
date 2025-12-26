<?php

use App\Exports\AlumnusExport;
use App\Imports\AlumnusImport;
use App\Models\Alumnus;
use App\Models\Department;
use App\Models\Tenure;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Maatwebsite\Excel\Facades\Excel;

beforeEach(function () {
    $this->admin = User::factory()->create(['is_admin' => true]);
    $this->user = User::factory()->create(['is_admin' => false]);
    $this->tenure = Tenure::factory()->create();
    $this->department = Department::factory()->create();
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

    it('generates dynamic filename with state filter', function () {
        $this->actingAs($this->admin)
            ->get(route('alumni.export', ['state' => 'Lagos']))
            ->assertOk()
            ->assertHeader('content-disposition');
    });

    it('generates dynamic filename with tenure filter', function () {
        $this->actingAs($this->admin)
            ->get(route('alumni.export', ['tenure_id' => $this->tenure->id]))
            ->assertOk();
    });

    it('exports with selected fields', function () {
        Alumnus::factory()->create();

        $this->actingAs($this->admin)
            ->get(route('alumni.export', ['fields' => ['name', 'email', 'department']]))
            ->assertOk();
    });
});

describe('Alumni Import', function () {
    it('redirects non-admin users from import', function () {
        Excel::fake();

        $this->actingAs($this->user)
            ->post(route('alumni.import.store'), [
                'file' => UploadedFile::fake()->create('alumni.xlsx'),
                'tenure_id' => $this->tenure->id,
            ])
            ->assertRedirect();
    });

    it('validates file is required', function () {
        $this->actingAs($this->admin)
            ->post(route('alumni.import.store'), [
                'tenure_id' => $this->tenure->id,
            ])
            ->assertSessionHasErrors(['file']);
    });

    it('validates tenure_id is required', function () {
        $this->actingAs($this->admin)
            ->post(route('alumni.import.store'), [
                'file' => UploadedFile::fake()->create('alumni.xlsx'),
            ])
            ->assertSessionHasErrors(['tenure_id']);
    });
});

describe('AlumnusImport Class', function () {
    it('creates department when importing unknown department', function () {
        $import = new AlumnusImport($this->tenure->id);

        $reflection = new \ReflectionMethod($import, 'findOrCreateDepartment');
        $reflection->setAccessible(true);

        $departmentName = 'New Department ' . uniqid();
        $departmentId = $reflection->invoke($import, $departmentName);

        expect($departmentId)->not->toBeNull();
        $this->assertDatabaseHas('departments', ['name' => $departmentName]);
    });

    it('finds existing department by name', function () {
        $import = new AlumnusImport($this->tenure->id);

        $reflection = new \ReflectionMethod($import, 'findOrCreateDepartment');
        $reflection->setAccessible(true);

        $departmentId = $reflection->invoke($import, $this->department->name);

        expect($departmentId)->toBe($this->department->id);
    });

    it('finds existing department by code', function () {
        $import = new AlumnusImport($this->tenure->id);

        $reflection = new \ReflectionMethod($import, 'findOrCreateDepartment');
        $reflection->setAccessible(true);

        $departmentId = $reflection->invoke($import, $this->department->code);

        expect($departmentId)->toBe($this->department->id);
    });

    it('returns null for empty department', function () {
        $import = new AlumnusImport($this->tenure->id);

        $reflection = new \ReflectionMethod($import, 'findOrCreateDepartment');
        $reflection->setAccessible(true);

        expect($reflection->invoke($import, ''))->toBeNull();
        expect($reflection->invoke($import, null))->toBeNull();
        expect($reflection->invoke($import, '   '))->toBeNull();
    });

    it('parses comma-separated phone numbers', function () {
        $import = new AlumnusImport($this->tenure->id);

        $reflection = new \ReflectionMethod($import, 'parsePhones');
        $reflection->setAccessible(true);

        $phones = $reflection->invoke($import, '08012345678, 08098765432');

        expect($phones)->toBeArray();
        expect($phones)->toHaveCount(2);
        expect($phones[0])->toBe('08012345678');
        expect($phones[1])->toBe('08098765432');
    });

    it('parses semicolon-separated phone numbers', function () {
        $import = new AlumnusImport($this->tenure->id);

        $reflection = new \ReflectionMethod($import, 'parsePhones');
        $reflection->setAccessible(true);

        $phones = $reflection->invoke($import, '08012345678; 08098765432');

        expect($phones)->toHaveCount(2);
    });

    it('parses day-month birth date format', function () {
        $import = new AlumnusImport($this->tenure->id);

        $reflection = new \ReflectionMethod($import, 'parseBirthDate');
        $reflection->setAccessible(true);

        // "20-12" should become "2000-12-20"
        $date = $reflection->invoke($import, '20-12');
        expect($date)->toBe('2000-12-20');

        // "20/12" should also work
        $date = $reflection->invoke($import, '20/12');
        expect($date)->toBe('2000-12-20');
    });

    it('parses day month name birth date format', function () {
        $import = new AlumnusImport($this->tenure->id);

        $reflection = new \ReflectionMethod($import, 'parseBirthDate');
        $reflection->setAccessible(true);

        // "20 Dec" should become "2000-12-20"
        $date = $reflection->invoke($import, '20 Dec');
        expect($date)->toBe('2000-12-20');

        // "20 December" should also work
        $date = $reflection->invoke($import, '20 December');
        expect($date)->toBe('2000-12-20');
    });

    it('returns null for invalid birth date', function () {
        $import = new AlumnusImport($this->tenure->id);

        $reflection = new \ReflectionMethod($import, 'parseBirthDate');
        $reflection->setAccessible(true);

        expect($reflection->invoke($import, 'invalid'))->toBeNull();
        expect($reflection->invoke($import, null))->toBeNull();
    });

    it('generates unique department code when duplicate exists', function () {
        // Create initial department with code "ND"
        Department::create([
            'name' => 'New Department',
            'code' => 'ND',
            'school' => 'Test',
        ]);

        $import = new AlumnusImport($this->tenure->id);

        $reflection = new \ReflectionMethod($import, 'findOrCreateDepartment');
        $reflection->setAccessible(true);

        // This should create "ND1" since "ND" exists
        $departmentId = $reflection->invoke($import, 'New Dept');

        $this->assertDatabaseHas('departments', ['code' => 'ND1']);
    });
});
