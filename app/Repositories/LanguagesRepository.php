<?php

namespace App\Repositories;

use App\Attributes\CachedMethod;
use App\Enums\CachedMethodTypesEnum;
use App\Services\CacheHelper;
use Cache;
use Domain\ModuleLanguageConstructions\Models\Construction;
use Domain\ModuleLanguageConstructions\Models\Language;
use Domain\ModuleLanguageConstructions\Repositories\LanguagesRepository as ILanguagesRepository;
use Illuminate\Pagination\LengthAwarePaginator;

readonly class LanguagesRepository implements ILanguagesRepository
{
    public function __construct(
        private CacheHelper $cacheHelper
    ) {
    }

    #[CachedMethod]
    /**
     * Возвращает отформатированные опции для select с языками
     *
     * @return array
     */
    public function getOptions(): array
    {
        return Cache::tags([Language::class])->rememberForever(
            $this->cacheHelper->makeKey([__METHOD__]),
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

    #[CachedMethod(type: CachedMethodTypesEnum::PAGINATION)]
    /**
     * Возвращает пагинацию для конкретной страницы
     *
     * @param int $page
     *
     * @return LengthAwarePaginator
     */
    public function getPagination(int $page): LengthAwarePaginator
    {
        return Cache::tags([Language::class])->rememberForever(
            $this->cacheHelper->makeKey([__METHOD__, $page]),
            function () use ($page) {
                /** @noinspection PhpUndefinedMethodInspection */
                return Language::paginate(10, pageName: config('pagination.languages_page_name'), page: $page);
            }
        );
    }

    /**
     * Возвращает язык программирования по коду
     *
     * @param string $slug
     *
     * @return Language|null
     */
    public function getBySlug(string $slug): ?Language
    {
        return Language::firstWhere('slug', $slug);
    }

    public function add(array $language): Language
    {
        /** @noinspection PhpUndefinedMethodInspection */
        $language = Language::create($language);
        Cache::tags([Language::class])->flush();

        return $language;
    }

    public function update(Language $language, array $fields): void
    {
        $language->update($fields);
        Cache::tags([Construction::class])->flush();
    }

    public function delete(Language $language): void
    {
        $language->delete();
        Cache::tags([Construction::class, Language::class])->flush();
    }

}
