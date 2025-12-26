<?php

namespace App\Models;

use App\Enums\NigerianState;
use App\Enums\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Alumnus extends Model
{
    use \Illuminate\Database\Eloquent\Factories\HasFactory;

    protected $guarded = ['id'];

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
     * Alumni executive positions (post-graduation leadership roles)
     */
    //    public function executivePositions(): HasMany
    //    {
    //        return $this->hasMany(AlumniExecutive::class);
    //    }
    /**
     * Get the communication logs for the alumnus.
     */
    public function communicationLogs(): HasMany
    {
        return $this->hasMany(CommunicationLog::class)->orderByDesc('occurred_at');
    }
}
