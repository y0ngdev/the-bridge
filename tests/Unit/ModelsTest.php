<?php

use App\Models\Alumnus;
use App\Models\Department;
use App\Models\Tenure;

describe('Alumnus Model', function () {
    beforeEach(function () {
        $this->tenure = Tenure::factory()->create();
        $this->department = Department::factory()->create();
    });

    it('belongs to a tenure', function () {
        $alumnus = Alumnus::factory()->create(['tenure_id' => $this->tenure->id]);

        expect($alumnus->tenure)->toBeInstanceOf(Tenure::class);
        expect($alumnus->tenure->id)->toBe($this->tenure->id);
    });

    it('belongs to a department', function () {
        $alumnus = Alumnus::factory()->create(['department_id' => $this->department->id]);

        expect($alumnus->department)->toBeInstanceOf(Department::class);
        expect($alumnus->department->id)->toBe($this->department->id);
    });

    it('casts phones to array', function () {
        $alumnus = Alumnus::factory()->create(['phones' => ['123456', '789012']]);

        expect($alumnus->phones)->toBeArray();
        expect($alumnus->phones)->toHaveCount(2);
    });

    it('casts is_futa_staff to boolean', function () {
        $alumnus = Alumnus::factory()->create(['is_futa_staff' => true]);

        expect($alumnus->is_futa_staff)->toBeBool();
        expect($alumnus->is_futa_staff)->toBeTrue();
    });
});

describe('Department Model', function () {
    it('returns options with id as value', function () {
        Department::query()->delete();
        $department = Department::factory()->create(['name' => 'Test Dept']);

        $options = Department::options();

        expect($options[0]['value'])->toBe($department->id);
        expect($options[0]['label'])->toBe('Test Dept');
    });
});

describe('Tenure Model', function () {
    it('has many alumni', function () {
        $tenure = Tenure::factory()->create();
        Alumnus::factory()->count(5)->create(['tenure_id' => $tenure->id]);

        expect($tenure->alumni)->toHaveCount(5);
    });
});
