<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Prize extends Model
{
    protected $fillable = ['name', 'sequence', 'status'];

    public function winner(): HasOne
    {
        return $this->hasOne(Winner::class);
    }
}
