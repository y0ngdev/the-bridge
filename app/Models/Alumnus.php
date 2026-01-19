<?php

namespace App\Models;

use App\Enums\NigerianState;
use App\Enums\Unit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alumnus extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $guarded = ['id'];

    protected $appends = ['initials'];

    protected function casts(): array
    {
        return [
            'phones' => 'array',
            'is_futa_staff' => 'boolean',
            'is_overseas' => 'boolean',
            'birth_date' => 'datetime:Y-m-d',
            'unit' => Unit::class,
            'state' => NigerianState::class,
        ];
    }

    /**
     * Get the initials of the alumnus name.
     */
    public function getInitialsAttribute(): string
    {
        // Use mb_split or regular explode (explode is usually fine if names are space-separated)
        $words = explode(' ', $this->name ?? '');
        $initials = '';

        foreach ($words as $w) {
            // mb_substr(string, start, length) is multi-byte safe
            $initials .= mb_strtoupper(mb_substr($w, 0, 1));
        }

        return mb_substr($initials, 0, 2);
    }

    /**
     * Scope to search by name or email.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (!$search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        });
    }

    /**
     * Scope to filter by tenure.
     */
    public function scopeByTenure(Builder $query, ?int $tenureId): Builder
    {
        if (!$tenureId) {
            return $query;
        }

        return $query->where('tenure_id', $tenureId);
    }

    /**
     * Scope to filter by unit.
     */
    public function scopeByUnit(Builder $query, ?string $unit): Builder
    {
        if (!$unit) {
            return $query;
        }

        return $query->where('unit', $unit);
    }

    /**
     * Scope to filter by state.
     */
    public function scopeByState(Builder $query, ?string $state): Builder
    {
        if (!$state) {
            return $query;
        }

        return $query->where('state', $state);
    }

    /**
     * Scope to filter by gender (case-insensitive).
     */
    public function scopeByGender(Builder $query, ?string $gender): Builder
    {
        if (!$gender) {
            return $query;
        }

        return $query->whereRaw('LOWER(gender) = ?', [strtolower($gender)]);
    }

    public function tenure(): BelongsTo
    {
        return $this->belongsTo(Tenure::class);
    }

    /**
     * Get the department this alumnus belongs to.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the communication logs for the alumnus.
     */
    public function communicationLogs(): HasMany
    {
        return $this->hasMany(CommunicationLog::class)->orderByDesc('occurred_at');
    }
}
