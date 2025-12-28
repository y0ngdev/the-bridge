<?php

namespace App\Models;

use App\Enums\RedemptionWeekDay;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RedemptionWeekAttendance extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'event_day' => RedemptionWeekDay::class,
        ];
    }

    /**
     * Get the session this attendance belongs to.
     */
    public function session(): BelongsTo
    {
        return $this->belongsTo(RedemptionWeekSession::class, 'session_id');
    }

    /**
     * Get the alumnus this attendance is for.
     */
    public function alumnus(): BelongsTo
    {
        return $this->belongsTo(Alumnus::class);
    }

    /**
     * Get the user who marked this attendance.
     */
    public function markedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}
