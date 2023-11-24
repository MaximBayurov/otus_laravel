<?php

namespace App\Http\Requests;

use App\Models\Construction;
use App\Services\LanguageService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateConstructionRequest extends FormRequest
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
            'id' => 'required',
            'slug' => [
                'required',
                'max:255',
                Rule::unique('constructions')->ignore($this->get('id')),
            ],
            'title' => ['required', 'max:255'],
            'description' => ['max:65535'],
            'languages.*.id' => ['sometimes', 'required', 'exists:App\Models\Language,id'],
            'languages.*.code' => ['sometimes', 'required'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        /**
         * @var $languageService LanguageService
         */
        $languageService = \App::get(LanguageService::class);

        $this->merge([
            "languages" => $languageService->filterEmpty($this->get('languages') ?? []),
        ]);
    }

    /**
     * Обработка запроса на обновление языковой конструкции
     * @param \App\Models\Construction $construction
     *
     * @return Construction
     */
    public function handle(Construction $construction): Construction
    {
        $construction->update($this->only(['slug', 'title', 'description']));
        if ($this->has('languages')) {
            $construction->languages()->detach();
            foreach ($this->get('languages') as $language) {
                $construction->languages()->attach($language['id'], [
                    'code' => $language['code']
                ]);
            }
        }
        return $construction;
    }
}
