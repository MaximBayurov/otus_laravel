<?php

namespace App\Http\Requests;

use App;
use Domain\ModuleLanguageConstructions\Models\Language;
use Domain\ModuleLanguageConstructions\Repositories\ConstructionLanguageRepository;
use Domain\ModuleLanguageConstructions\Repositories\LanguagesRepository;
use Domain\ModuleLanguageConstructions\Services\ConstructionImplementationsService;
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
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
         * @var $implementationsService ConstructionImplementationsService
         */
        $implementationsService = App::get(ConstructionImplementationsService::class);

        $this->merge([
            "constructions" => $implementationsService->filterEmpty($this->get('constructions') ?? []),
        ]);
    }

    /**
     * Обработка запроса на обновление языка программирования
     * @param Language $language
     *
     * @return Language
     */
    public function handle(Language $language): Language
    {
        /**
         * @var LanguagesRepository $languagesRepository
         */
        $languagesRepository = App::get(LanguagesRepository::class);
        $languagesRepository->update($language, $this->only(['title', 'slug', 'description']));

        /**
         * @var ConstructionLanguageRepository $implementationsRepository
         */
        $implementationsRepository = App::get(ConstructionLanguageRepository::class);
        $implementationsRepository->updateForLanguage($language, $this->get('constructions', []));

        return $language;
    }
}
