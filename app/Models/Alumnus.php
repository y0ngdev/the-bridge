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

    protected $appends = ['initials', 'photo_url'];

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
     * Get the full URL to the alumni photo.
     */
    public function getPhotoUrlAttribute(): ?string
    {
        if (!$this->photo) {
            return null;
        }

        return asset('storage/' . $this->photo);
    }

    /**
     * Scope to search by name, email, or phone number.
     */
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (!$search) {
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
     * Find potential duplicate alumni records (optimized, returns pairs only).
     */
    public static function findDuplicates(): array
    {
        $duplicatePairs = [];
        $processed = [];

        // First, find exact email matches (fast database query)
        $emailDuplicates = self::notMerged()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->select('email')
            ->groupBy('email')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('email');

        if ($emailDuplicates->isNotEmpty()) {
            $records = self::notMerged()
                ->whereIn('email', $emailDuplicates)
                ->with('department', 'tenure')
                ->get()
                ->groupBy('email');

            foreach ($records as $email => $group) {
                // Create pairs from matching records
                $count = $group->count();
                for ($i = 0; $i < $count; $i++) {
                    for ($j = $i + 1; $j < $count; $j++) {
                        $rec1 = $group[$i];
                        $rec2 = $group[$j];
                        $id1 = $rec1->id;
                        $id2 = $rec2->id;
                        $pairKey = min($id1, $id2) . '-' . max($id1, $id2);

                        if (!isset($processed[$pairKey])) {
                            $duplicatePairs[] = [$rec1, $rec2];
                            $processed[$pairKey] = true;
                        }
                    }
                }
            }
        }

        // Then check for name similarity and phone overlaps
        $alumni = self::notMerged()
            ->with('department', 'tenure')
            ->orderBy('name')
            ->limit(200)
            ->get();

        // Pre-calculate normalized values to avoid O(N^2) normalization
        $normalizedMap = [];
        foreach ($alumni as $a) {
            $normalizedMap[$a->id] = [
                'name' => self::normalizeName($a->name),
                'phones' => array_map('self::normalizePhone', $a->phones ?? []),
            ];
        }

        foreach ($alumni as $i => $alumnus) {
            // Only check subsequent records (avoid duplicate pairs)
            for ($j = $i + 1; $j < $alumni->count(); $j++) {
                $candidate = $alumni[$j];
                $id1 = $alumnus->id;
                $id2 = $candidate->id;
                $pairKey = min($id1, $id2) . '-' . max($id1, $id2);

                if (isset($processed[$pairKey])) {
                    continue;
                }

                $isDuplicate = false;

                // Use pre-calculated match
                $name1 = $normalizedMap[$id1]['name'];
                $name2 = $normalizedMap[$id2]['name'];

                // Check if names are very similar
                if ($name1 === $name2) {
                    $isDuplicate = true;
                } elseif (strlen($name1) >= 5 && strlen($name2) >= 5) {
                    // Use similar_text for fuzzy matching on longer names
                    similar_text($name1, $name2, $percent);
                    if ($percent >= 85) {
                        $isDuplicate = true;
                    }
                }

                // Phone overlap
                if (!$isDuplicate && !empty($normalizedMap[$id1]['phones']) && !empty($normalizedMap[$id2]['phones'])) {
                    $overlap = array_intersect(
                        $normalizedMap[$id1]['phones'],
                        $normalizedMap[$id2]['phones']
                    );
                    if (!empty($overlap)) {
                        $isDuplicate = true;
                    }
                }

                if ($isDuplicate) {
                    $duplicatePairs[] = [$alumnus, $candidate];
                    $processed[$pairKey] = true;
                }
            }
        }

        return $duplicatePairs;
    }

    /**
     * Normalize a name for comparison.
     */
    private static function normalizeName(string $name): string
    {
        $name = strtolower(trim($name));
        // Remove common titles
        $name = preg_replace('/^(mr\.?|mrs\.?|ms\.?|dr\.?|prof\.?|engr\.?)\s*/i', '', $name);
        // Remove extra spaces
        $name = preg_replace('/\s+/', ' ', $name);
        return $name;
    }

    /**
     * Normalize a phone number for comparison.
     */
    private static function normalizePhone(string $phone): string
    {
        // Remove all non-digit characters
        return preg_replace('/\D/', '', $phone);
    }
}
