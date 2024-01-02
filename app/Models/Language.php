<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Language extends BaseModel
{
    use HasFactory;

    const CACHE_TAG = 'languages';

    protected $casts = [
        'updated_at'  => 'datetime:Y-m-d H:m:s',
        'created_at' => 'datetime:Y-m-d H:m:s',
    ];

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
