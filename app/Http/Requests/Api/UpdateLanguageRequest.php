<?php

namespace App\Http\Requests\Api;

use Domain\ModuleLanguageConstructions\Repositories\LanguagesRepository;
use Illuminate\Validation\Rule;

class UpdateLanguageRequest extends \App\Http\Requests\UpdateLanguageRequest
{
    /** @inheritDoc */
    protected function getSlugRules(): array
    {
        $languageRepository = app()->get(LanguagesRepository::class);
        $language = $languageRepository->getBySlug($this->route('language'));

        $slugRules = [
            'required',
            'max:255',
        ];
        $slugRules[] = !empty($language)
            ? Rule::unique('languages')->ignore($language->id)
            : Rule::unique('languages');
        return $slugRules;
    }
}
