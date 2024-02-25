<?php

namespace App\Http\Requests;

use App;
use Domain\ModuleLanguageConstructions\Models\Construction;
use Domain\ModuleLanguageConstructions\Repositories\ConstructionLanguageRepository;
use Domain\ModuleLanguageConstructions\Repositories\ConstructionsRepository;
use Domain\ModuleLanguageConstructions\Services\ConstructionImplementationsService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;

class StoreConstructionRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'slug' => ['required', 'max:255', 'unique:App\Models\Construction,slug'],
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
         * @var $implementationsService ConstructionImplementationsService
         */
        $implementationsService = App::get(ConstructionImplementationsService::class);

        $this->merge([
            "languages" => $implementationsService->filterEmpty($this->get('languages') ?? []),
        ]);
    }

    /**
     * Обработка запроса на создание языковой конструкции
     *
     * @return null|Construction
     */
    public function handle(): ?Construction
    {
        /**
         * @var ConstructionsRepository $constructionsRepository
         */
        $constructionsRepository = App::get(ConstructionsRepository::class);
        $construction = $constructionsRepository->add($this->only(['title', 'slug', 'description']));
        if (empty($construction)) {
            return null;
        }

        /**
         * @var ConstructionLanguageRepository $implementationsRepository
         */
        $implementationsRepository = App::get(ConstructionLanguageRepository::class);
        $implementationsRepository->addForConstruction($construction, $this->get('languages', []));

        return $construction;
    }
}
