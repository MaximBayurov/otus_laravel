<?php

namespace App\Http\Requests;

use App;
use Domain\ModuleLanguageConstructions\Models\Construction;
use Domain\ModuleLanguageConstructions\Repositories\ConstructionLanguageRepository;
use Domain\ModuleLanguageConstructions\Repositories\ConstructionsRepository;
use Domain\ModuleLanguageConstructions\Services\ConstructionImplementationsService;
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function rules(): array
    {
        $construction = $this->route('construction');
        if (!is_a($construction, Construction::class)) {
            $languageRepository = app()->get(ConstructionsRepository::class);
            $construction = $languageRepository->getBySlug($construction);
        }

        $slug = [
            'required',
            'max:255',
        ];
        $slug[] = !empty($construction)
            ? Rule::unique('constructions')->ignore($construction->id)
            : Rule::unique(
                'constructions'
            );

        return [
            'id' => 'required',
            'slug' => $slug,
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
     * Обработка запроса на обновление языковой конструкции
     *
     * @param \Domain\ModuleLanguageConstructions\Models\Construction $construction
     *
     * @return null|Construction
     */
    public function handle(Construction $construction): ?Construction
    {
        /**
         * @var ConstructionsRepository $constructionsRepository
         */
        $constructionsRepository = App::get(ConstructionsRepository::class);
        $result = $constructionsRepository->update($construction, $this->only(['title', 'slug', 'description']));
        if (!$result) {
            return null;
        }

        /**
         * @var ConstructionLanguageRepository $implementationsRepository
         */
        $implementationsRepository = App::get(ConstructionLanguageRepository::class);
        $implementationsRepository->updateForConstruction($construction, $this->get('languages', []));

        return $construction;
    }
}
