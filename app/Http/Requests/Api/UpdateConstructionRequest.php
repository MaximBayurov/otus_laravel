<?php

namespace App\Http\Requests\Api;

use Domain\ModuleLanguageConstructions\Repositories\ConstructionsRepository;
use Illuminate\Validation\Rule;

class UpdateConstructionRequest extends \App\Http\Requests\UpdateConstructionRequest
{
    /** @inheritDoc */
    protected function getSlugRules(): array
    {
        $languageRepository = app()->get(ConstructionsRepository::class);
        $construction = $languageRepository->getBySlug($this->route('construction'));

        $slugRules = [
            'required',
            'max:255',
        ];
        $slugRules[] = !empty($construction)
            ? Rule::unique('constructions')->ignore($construction->id)
            : Rule::unique('constructions');
        return $slugRules;
    }
}
