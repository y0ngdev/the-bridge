<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DismissedDuplicate extends Model
{
    protected $guarded = ['id'];

    public function alumnus1(): BelongsTo
    {
        return $this->belongsTo(Alumnus::class, 'alumnus_id_1');
    }

    public function alumnus2(): BelongsTo
    {
        return $this->belongsTo(Alumnus::class, 'alumnus_id_2');
    }

    public function dismissedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dismissed_by');
    }

    /**
     * Check if a pair of alumni IDs has been dismissed.
     */
    public static function isPairDismissed(int $id1, int $id2): bool
    {
        // Always store with smaller ID first for consistency
        $min = min($id1, $id2);
        $max = max($id1, $id2);

        return self::where('alumnus_id_1', $min)
            ->where('alumnus_id_2', $max)
            ->exists();
    }

    /**
     * Dismiss a pair of alumni as not duplicates.
     */
    public static function dismissPair(int $id1, int $id2, ?int $userId = null): self
    {
        $min = min($id1, $id2);
        $max = max($id1, $id2);

        return self::firstOrCreate(
            ['alumnus_id_1' => $min, 'alumnus_id_2' => $max],
            ['dismissed_by' => $userId]
        );
    }
}
