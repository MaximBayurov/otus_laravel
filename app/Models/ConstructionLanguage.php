<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Scout\Searchable;

class ConstructionLanguage extends BaseModel
{
    protected $table = 'construction_language';

    use Searchable;

    public function searchableAs(): string
    {
        return 'construction_language_index';
    }

    public function toSearchableArray()
    {
        return [
            'id' => (int) $this->id,
            'code' => $this->code,
            'language_title' => $this->language->title,
            'language_slug' => $this->language->slug,
            'construction_title' => $this->construction->title,
            'construction_slug' => $this->construction->slug,
        ];
    }

    public function language(): HasOne
    {
        return $this->hasOne(Language::class , 'id', 'language_id');
    }

    public function construction(): HasOne
    {
        return $this->hasOne(Construction::class , 'id', 'construction_id');
    }
}
