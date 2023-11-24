<?php

namespace App\Http\Requests;

use App\Models\Construction;
use App\Models\Language;
use App\Services\ConstructionService;
use App\Services\LanguageService;
use Illuminate\Foundation\Http\FormRequest;

class StoreLanguageRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'slug' => ['required', 'max:255', 'unique:App\Models\Language,slug'],
            'title' => ['required', 'max:255'],
            'description' => ['max:65535'],
            'constructions.*.id' => ['sometimes', 'required', 'exists:App\Models\Construction,id'],
            'constructions.*.code' => ['sometimes', 'required'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        /**
         * @var $constructionService ConstructionService
         */
        $constructionService = \App::get(ConstructionService::class);

        $this->merge([
            "constructions" => $constructionService->filterEmpty($this->get('constructions') ?? []),
        ]);
    }

    /**
     * Обработка запроса на создание языка программирования
     *
     * @return \App\Models\Language
     */
    public function handle(): Language
    {
        $language = Language::create($this->only(['title', 'slug', 'description']));
        if ($this->has('constructions')) {
            foreach ($this->get('constructions') as $construction) {
                $language->constructions()->attach($construction['id'], [
                    'code' => $construction['code']
                ]);
            }
            \Cache::tags([Construction::CACHE_TAG])->flush();
        }
        \Cache::tags([Language::CACHE_TAG])->flush();
        return $language;
    }
}
