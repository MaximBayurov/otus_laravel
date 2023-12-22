<?php

namespace App\Services;

use App\Attributes\CachedForModelMethod;
use App\Attributes\CachedMethod;
use App\Attributes\CachedPaginationMethod;
use App\Models\Construction;
use App\Models\Language;
use Cache;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Сервис для работы с сущностью "Языки программирования"
 */
class LanguageService implements PaginationService
{
    const PAGE_NAME = 'languages-page';

    public function __construct(
        private CacheHelper $cacheHelper
    ) {
    }

    #[CachedMethod(key:'language:options')]
    /**
     * Возвращает отформатированные опции для селекта с языками
     * @return array
     */
    public function getLanguageOptions(): array
    {
        return Cache::tags([Language::CACHE_TAG])->rememberForever(
            $this->cacheHelper->makeKey(['language:options']),
            function () {
                $result = [];
                foreach (Language::all(['id', 'title']) as $language) {
                    $result[] = [
                        'value' => $language->id,
                        'title' => $language->title,
                    ];
                }
                return $result;
            }
        );
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
        }

        return $this->collectLanguagesFormattedFor($construction);
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

    #[CachedPaginationMethod()]
    #[CachedMethod(key:'language:list')]
    /**
     * Возвращает пагинацию для конкретной страницы
     * @param int $page
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPagination(int $page): LengthAwarePaginator
    {
        return Cache::tags([Language::CACHE_TAG])->rememberForever(
            $this->cacheHelper->makeKey(['language:list', $page]),
            function () use ($page) {
                return Language::paginate(10, pageName: self::PAGE_NAME, page:$page);
            }
        );
    }

    #[CachedMethod(key:'construction:languages:formatted')]
    #[CachedForModelMethod(model: Construction::class)]
    /**
     * Возвращает массив отформатированных языков программирования для языковой конструкции
     * @param \App\Models\Construction $construction
     *
     * @return array
     */
    public function collectLanguagesFormattedFor(Construction $construction): array
    {
        return Cache::tags([Construction::CACHE_TAG, Language::CACHE_TAG])->rememberForever(
            $this->cacheHelper->makeKey(['construction:languages:formatted', $construction->id]),
            function () use ($construction) {
                return array_map(function ($item) {
                    return [
                        'id' => $item['pivot']['language_id'],
                        'code' => $item['pivot']['code'],
                    ];
                }, $construction->languages->toArray());
            }
        );
    }
}
