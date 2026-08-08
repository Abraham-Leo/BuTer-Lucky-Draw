<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'google_id', 'avatar', 'role',
    ];

    protected $hidden = [
        'remember_token',
    ];

    public function participant(): HasOne
    {
        return $this->hasOne(Participant::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isOperator(): bool
    {
        return $this->role === 'operator';
    }

    public function canManageDraw(): bool
    {
        return in_array($this->role, ['admin', 'operator']);
    }
}
