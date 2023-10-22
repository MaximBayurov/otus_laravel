<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Construction extends BaseModel
{
    use HasFactory;

    public function languages(): BelongsToMany
    {
        return $this->belongsToMany(Language::class);
    }
}
