<?php

namespace Domain\ModuleLanguageConstructions\Services;

use App\Repositories\ConstructionLanguageRepository;
use Domain\ModuleLanguageConstructions\Models\Construction;
use Domain\ModuleLanguageConstructions\Models\Language;

/**
 * Сервис для работы с реализациями конструкций в языках программирования
 */
readonly class ConstructionImplementationsService
{

    public function __construct(
        private ConstructionLanguageRepository $constructionLanguageRepository
    )
    {
    }

    /**
     * Отфильтровывает пустые реализации
     *
     * @param array $implementations
     *
     * @return array
     */
    public function filterEmpty(
        array $implementations
    ): array {
        return array_values(
            array_filter($implementations, function ($item) {
                return !empty($item['code']) || !empty($item['id']);
            })
        );
    }

    /**
     * Возвращает реализации языковых конструкций в языке программирования в форматированном для отображения виде
     *
     * @param \Domain\ModuleLanguageConstructions\Models\Language $language
     * @param array $oldConstructions
     *
     * @return array
     */
    public function getFormattedForLanguage(Language $language, array $oldConstructions): array
    {
        $oldConstructions = $this->filterEmpty($oldConstructions);
        if (!empty($oldConstructions)) {
            return $oldConstructions;
        }

        return $this->constructionLanguageRepository->collectConstructionsFormattedFor($language);
    }

    /**
     * Возвращает реализации языковой конструкции во всех языках программирования в форматированном для отображения виде
     *
     * @param Construction $construction
     * @param array $oldLanguages
     *
     * @return array
     */
    public function getFormattedForConstruction(Construction $construction, array $oldLanguages): array
    {
        $oldLanguages = $this->filterEmpty($oldLanguages);
        if (!empty($oldLanguages)) {
            return $oldLanguages;
        }

        return $this->constructionLanguageRepository->collectLanguagesFormattedFor($construction);
    }
}
