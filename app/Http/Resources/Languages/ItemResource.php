<?php

namespace App\Http\Resources\Languages;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'title' => $this->title,
            'description' => $this->description,
            'codes' => $this->when(
                !empty($this->additional["codes"]) && !empty($this->pivot),
                function () {
                    return $this->additional["codes"];
                }
            ),
        ];
    }
}
