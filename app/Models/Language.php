<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Language extends BaseModel
{
    use HasFactory;

    const CACHE_TAG = 'languages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'slug',
        'title',
        'description',
    ];

    public function constructions(): BelongsToMany
    {
        return $this->belongsToMany(Construction::class)->withPivot('code');
    }
}
