<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Participant extends Model
{
    protected $fillable = [
        'user_id', 'ticket_number', 'registered_at', 'is_winner',
    ];

    protected $casts = [
        'registered_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function winner(): HasOne
    {
        return $this->hasOne(Winner::class);
    }

    /**
     * Generate a unique 4-digit ticket number (random, not sequential —
     * see saran "Nomor Undian Acak" in the spec).
     */
    public static function generateUniqueTicketNumber(): string
    {
        do {
            $candidate = str_pad((string) random_int(0, 9999), 4, '0', STR_PAD_LEFT);
        } while (self::where('ticket_number', $candidate)->exists());

        return $candidate;
    }
}
