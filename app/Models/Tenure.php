<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenure extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $fillable = ['name', 'year', 'is_active', 'start_date', 'end_date'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function alumni(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Alumnus::class);
    }

    /**
     * Scope for the currently active tenure/session.
     */
    public function scopeActive($query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Ensure only one tenure is active at a time. Deactivate others before activating.
     */
    protected static function booted(): void
    {
        static::saving(function (Tenure $tenure) {
            if ($tenure->is_active) {
                Tenure::where('id', '!=', $tenure->id)->update(['is_active' => false]);
            }
        });
    }
}
