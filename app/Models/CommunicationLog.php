<?php

namespace App\Models;

use App\Enums\CommunicationOutcome;
use App\Enums\CommunicationType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationLog extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'type' => CommunicationType::class,
            'outcome' => CommunicationOutcome::class,
            'occurred_at' => 'datetime',
        ];
    }

    public function alumnus(): BelongsTo
    {
        return $this->belongsTo(Alumnus::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
