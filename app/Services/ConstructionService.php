<?php

namespace App\Services;

use App\Models\Construction;
use App\Models\Language;

/**
 * Сервис для работы с сущностью "Языковые конструкции"
 */
class ConstructionService
{
    public function __construct(
        private CacheHelper $cacheHelper
    ) {
    }

    /**
     * Возвращает отформатированные опции для селекта с конструкциями
     * @return array
     */
    public function getConstructionOptions(): array
    {
        return \Cache::tags([Construction::CACHE_TAG])->rememberForever(
            $this->cacheHelper->makeKey(['constructions:options']),
            function () {
                $result = [];
                foreach (Construction::all(['id', 'title']) as $construction) {
                    $result[] = [
                        'value' => $construction->id,
                        'title' => $construction->title,
                    ];
                }
                return $result;
            }
        );
    }

    /**
     * Возвращает конструкции языка в форматированном для отображения виде
     *
     * @param \App\Models\Language $language
     *
     * @return array
     */
    public function getConstructionsFormatted(Language $language): array
    {
        $oldConstructions = $this->filterEmpty(old('constructions') ?? []);
        if (!empty($oldConstructions)) {
            return $oldConstructions;
        }

        return \Cache::tags([Construction::CACHE_TAG, Language::CACHE_TAG])->rememberForever(
            $this->cacheHelper->makeKey(['language:constructions:formatted', $language->id]),
            function () use ($language) {
                return array_map(function ($item) {
                    return [
                        'id' => $item['pivot']['construction_id'],
                        'code' => $item['pivot']['code'],
                    ];
                }, $language->constructions->toArray());
            }
        );
    }

    /**
     * Отфильтровывает пустые массивы
     * @param array $constructions
     *
     * @return array
     */
    public function filterEmpty(array $constructions): array
    {
        return array_values(array_filter($constructions, function ($item) {
            return !empty($item['code']) || !empty($item['id']);
        }));
    }
}
