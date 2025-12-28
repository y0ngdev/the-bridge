<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RedemptionWeekSession extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the formatted session name (e.g., RW'25).
     */
    public function getFormattedNameAttribute(): string
    {
        $shortYear = substr((string) $this->year, -2);

        return "RW'{$shortYear}";
    }

    /**
     * Get all attendances for this session.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(RedemptionWeekAttendance::class, 'session_id');
    }

    /**
     * Scope for the currently active session.
     */
    public function scopeActive($query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by year.
     */
    public function scopeYear($query, int $year): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('year', $year);
    }

    /**
     * Ensure only one session is active at a time.
     */
    protected static function booted(): void
    {
        static::saving(function (RedemptionWeekSession $session) {
            if ($session->is_active) {
                RedemptionWeekSession::where('id', '!=', $session->id)->update(['is_active' => false]);
            }
        });
    }

    /**
     * Get attendance statistics for this session.
     *
     * @return array<string, mixed>
     */
    public function getAttendanceStats(): array
    {
        $attendances = $this->attendances()
            ->selectRaw('event_day, COUNT(*) as count')
            ->groupBy('event_day')
            ->pluck('count', 'event_day')
            ->toArray();

        $days = \App\Enums\RedemptionWeekDay::ordered();
        $stats = [];

        foreach ($days as $day) {
            $stats[$day->value] = [
                'label' => $day->label(),
                'count' => $attendances[$day->value] ?? 0,
            ];
        }

        return $stats;
    }

    /**
     * Get total unique attendees across all days.
     */
    public function getTotalAttendeesAttribute(): int
    {
        return $this->attendances()->distinct('alumnus_id')->count('alumnus_id');
    }

    /**
     * Get count of alumni with perfect attendance (all 7 days).
     */
    public function getPerfectAttendanceCountAttribute(): int
    {
        return $this->attendances()
            ->selectRaw('alumnus_id, COUNT(DISTINCT event_day) as days_attended')
            ->groupBy('alumnus_id')
            ->havingRaw('days_attended = 7')
            ->count();
    }
}
