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
     * Scope to search by name, email, or phone number.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhereJsonContains('phones', $search);
        });
    }

    /**
     * Scope to filter by tenure.
     */
    public function scopeByTenure(Builder $query, ?int $tenureId): Builder
    {
        if (! $tenureId) {
            return $query;
        }

        return $query->where('tenure_id', $tenureId);
    }

    /**
     * Scope to filter by unit.
     */
    public function scopeByUnit(Builder $query, ?string $unit): Builder
    {
        if (! $unit) {
            return $query;
        }

        return $query->where('unit', $unit);
    }

    /**
     * Scope to filter by state.
     */
    public function scopeByState(Builder $query, ?string $state): Builder
    {
        if (! $state) {
            return $query;
        }

        return $query->where('state', $state);
    }

    /**
     * Scope to filter by gender (case-insensitive).
     */
    public function scopeByGender(Builder $query, ?string $gender): Builder
    {
        if (! $gender) {
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

    /**
     * Scope to exclude merged records.
     */
    public function scopeNotMerged(Builder $query): Builder
    {
        return $query->whereNull('merged_into');
    }

    /**
     * Get the alumnus this record was merged into.
     */
    public function mergedInto(): BelongsTo
    {
        return $this->belongsTo(Alumnus::class, 'merged_into');
    }

    /**
     * Get alumni that were merged into this record.
     */
    public function mergedRecords(): HasMany
    {
        return $this->hasMany(Alumnus::class, 'merged_into');
    }

    /**
     * Find potential duplicate alumni records (optimized).
     */
    public static function findDuplicates(): array
    {
        $duplicateGroups = [];
        $processed = [];

        // First, find exact email matches (fast database query)
        $emailDuplicates = self::notMerged()
            ->whereNotNull('email')
            ->select('email')
            ->groupBy('email')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('email');

        foreach ($emailDuplicates as $email) {
            $group = self::notMerged()
                ->where('email', $email)
                ->with('department', 'tenure')
                ->get();

            if ($group->count() > 1) {
                $duplicateGroups[] = $group->all();
                $processed = array_merge($processed, $group->pluck('id')->all());
            }
        }

        // Then check for name similarity and phone overlaps (limit to avoid timeout)
        $alumni = self::notMerged()
            ->whereNotIn('id', $processed)
            ->with('department', 'tenure')
            ->limit(50) // Process only first 50 to avoid timeout
            ->get();

        foreach ($alumni as $alumnus) {
            if (in_array($alumnus->id, $processed)) {
                continue;
            }

            $potentialDuplicates = [];

            // Check remaining alumni for matches
            $candidates = self::notMerged()
                ->where('id', '!=', $alumnus->id)
                ->whereNotIn('id', $processed)
                ->limit(100)
                ->get();

            foreach ($candidates as $candidate) {
                $isDuplicate = false;

                // Simple name similarity check (first 5 characters match)
                $name1 = strtolower(substr($alumnus->name, 0, 5));
                $name2 = strtolower(substr($candidate->name, 0, 5));
                if ($name1 === $name2 && strlen($name1) >= 3) {
                    $isDuplicate = true;
                }

                // Phone overlap
                if ($alumnus->phones && $candidate->phones) {
                    $overlap = array_intersect($alumnus->phones, $candidate->phones);
                    if (! empty($overlap)) {
                        $isDuplicate = true;
                    }
                }

                if ($isDuplicate) {
                    if (empty($potentialDuplicates)) {
                        $potentialDuplicates[] = $alumnus;
                    }
                    $potentialDuplicates[] = $candidate;
                    $processed[] = $candidate->id;
                }
            }

            if (! empty($potentialDuplicates)) {
                $processed[] = $alumnus->id;
                $duplicateGroups[] = $potentialDuplicates;
            }
        }

        return $duplicateGroups;
    }
}
