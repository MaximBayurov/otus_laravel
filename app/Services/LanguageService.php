<?php

namespace App\Services;

use App\Models\Construction;
use App\Models\Language;

/**
 * Сервис для работы с сущностью "Языки программирования"
 */
class LanguageService
{
    /**
     * Возвращает отформатированные опции для селекта с языками
     * @return array
     */
    public function getLanguageOptions(): array
    {
        $result = [];
        foreach (Language::all(['id', 'title']) as $language) {
            $result[] = [
                'value' => $language->id,
                'title' => $language->title,
            ];
        }
        return $result;
    }

    /**
     * Возвращает языки программирования в форматированном для отображения виде
     *
     * @param \App\Models\Construction $construction
     *
     * @return array
     */
    public function getLanguagesFormatted(Construction $construction): array
    {
        $oldLanguages = $this->filterEmpty(old('languages') ?? []);
        if (!empty($oldLanguages)) {
            return $oldLanguages;
        } else {
            return array_map(function ($item) {
                return [
                    'id' => $item['pivot']['language_id'],
                    'code' => $item['pivot']['code'],
                ];
            }, $construction->languages->toArray());
        }
    }

    /**
     * Отфильтровывает пустые массивы языков
     * @param array $languages
     *
     * @return array
     */
    public function filterEmpty(array $languages): array
    {
        return array_values(array_filter($languages, function ($item) {
            return !empty($item['code']) || !empty($item['id']);
        }));
    }
}
