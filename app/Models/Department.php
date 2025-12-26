<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'school',
    ];

    /**
     * Get all alumni in this department.
     */
    public function alumni(): HasMany
    {
        return $this->hasMany(Alumnus::class);
    }

    /**
     * Get departments grouped by school.
     */
    public static function bySchool(): array
    {
        return self::orderBy('school')
            ->orderBy('name')
            ->get()
            ->groupBy('school')
            ->toArray();
    }

    /**
     * Get options for dropdown.
     */
    public static function options(): array
    {
        return self::orderBy('name')
            ->get()
            ->map(fn ($dept) => ['value' => $dept->id, 'label' => $dept->name])
            ->toArray();
    }
}
