<?php

namespace App\Http\Requests;

use App;
use Domain\ModuleLanguageConstructions\Models\Language;
use Domain\ModuleLanguageConstructions\Repositories\ConstructionLanguageRepository;
use Domain\ModuleLanguageConstructions\Repositories\LanguagesRepository;
use Domain\ModuleLanguageConstructions\Services\ConstructionImplementationsService;
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
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
         * @var $implementationsService ConstructionImplementationsService
         */
        $implementationsService = App::get(ConstructionImplementationsService::class);

        $this->merge([
            "constructions" => $implementationsService->filterEmpty($this->get('constructions') ?? []),
        ]);
    }

    /**
     * Обработка запроса на создание языка программирования
     *
     * @return Language
     */
    public function handle(): Language
    {
        /**
         * @var LanguagesRepository $languagesRepository
         */
        $languagesRepository = App::get(LanguagesRepository::class);
        $language = $languagesRepository->add($this->only(['title', 'slug', 'description']));

        /**
         * @var ConstructionLanguageRepository $implementationsRepository
         */
        $implementationsRepository = App::get(ConstructionLanguageRepository::class);
        $implementationsRepository->addForLanguage($language, $this->get('constructions', []));

        return $language;
    }
}
