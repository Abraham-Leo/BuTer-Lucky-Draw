<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Winner extends Model
{
    protected $fillable = [
        'participant_id',
        'prize_id',
        'draw_time',
        'draw_by',
        'is_cancelled',
    ];

    protected $casts = [
        'draw_time' => 'datetime',
        'is_cancelled' => 'boolean',
    ];

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Participant::class);
    }

    public function prize(): BelongsTo
    {
        return $this->belongsTo(Prize::class);
    }

    public function drawnBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'draw_by');
    }
}