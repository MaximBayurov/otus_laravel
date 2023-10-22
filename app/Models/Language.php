<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Language extends BaseModel
{
    use HasFactory;

    public function constructions(): BelongsToMany
    {
        return $this->belongsToMany(Construction::class);
    }
}
