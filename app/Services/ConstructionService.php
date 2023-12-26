<?php

namespace App\Services;

use App\Attributes\CachedMethod;
use App\Enums\CachedMethodTypesEnum;
use App\Models\Construction;
use App\Models\Language;
use Cache;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Сервис для работы с сущностью "Языковые конструкции"
 */
class ConstructionService implements PaginationService
{
    const PAGE_NAME = 'constructions-page';

    public function __construct(
        private CacheHelper $cacheHelper
    ) {
    }

    #[CachedMethod(key:'constructions:options')]
    /**
     * Возвращает отформатированные опции для селекта с конструкциями
     * @return array
     */
    public function getConstructionOptions(): array
    {
        return Cache::tags([Construction::CACHE_TAG])->rememberForever(
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

        return $this->collectConstructionsFormattedFor($language);
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

    #[CachedMethod(key:'constructions:list', type: CachedMethodTypesEnum::PAGINATION)]
    /**
     * Возвращает пагинацию для конкретной страницы
     * @param int $page
     *
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    public function getPagination(int $page): LengthAwarePaginator
    {
        return Cache::tags([Construction::CACHE_TAG])->rememberForever(
            $this->cacheHelper->makeKey(['constructions:list', $page]),
            function () use ($page) {
                return Construction::paginate(10, pageName: self::PAGE_NAME, page:$page);
            }
        );
    }

    #[CachedMethod(
        key:'language:constructions:formatted',
        type: CachedMethodTypesEnum::FOR_MODEL,
        model: Language::class
    )]
    /**
     * Возвращает массив отформатированных языковых конструкций для языка программирования
     *
     * @param \App\Models\Language $language
     *
     * @return array
     */
    public function collectConstructionsFormattedFor(Language $language): array
    {
        return Cache::tags([Construction::CACHE_TAG, Language::CACHE_TAG])->rememberForever(
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
}
