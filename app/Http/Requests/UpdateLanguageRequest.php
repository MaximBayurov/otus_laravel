<?php

namespace App\Http\Requests;

use App\Models\Language;
use App\Services\ConstructionService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLanguageRequest extends FormRequest
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
                Rule::unique('languages')->ignore($this->get('id')),
            ],
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
     * Обработка запроса на обновление языка программирования
     * @param \App\Models\Language $language
     *
     * @return \App\Models\Language
     */
    public function handle(Language $language): Language
    {
        $language->update($this->only(['slug', 'title', 'description']));
        if ($this->has('constructions')) {
            $language->constructions()->detach();
            foreach ($this->get('constructions') as $construction) {
                $language->constructions()->attach($construction['id'], [
                    'code' => $construction['code']
                ]);
            }
        }
        return $language;
    }
}
