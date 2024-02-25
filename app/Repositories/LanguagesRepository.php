<?php

namespace App\Repositories;

use App\Attributes\CachedMethod;
use App\Enums\CachedMethodTypesEnum;
use App\Enums\PageSizesEnum;
use App\Services\CacheHelper;
use Cache;
use Domain\ModuleLanguageConstructions\Models\Construction;
use Domain\ModuleLanguageConstructions\Models\Language;
use Domain\ModuleLanguageConstructions\Repositories\LanguagesRepository as ILanguagesRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

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
    public function getPagination(int $page, PageSizesEnum $pageSize = PageSizesEnum::SIZE_20): LengthAwarePaginator
    {
        return Cache::tags([Language::class])->rememberForever(
            $this->cacheHelper->makeKey([__METHOD__, $page, $pageSize->value]),
            function () use ($page, $pageSize) {
                /** @noinspection PhpUndefinedMethodInspection */
                return Language::paginate($pageSize->value, pageName: config('pagination.languages_page_name'), page: $page);
            }
        );
    }

    public function getAll(): Collection
    {
        return Language::all();
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
        return Language::firstWhere('slug', '=', $slug);
    }

    public function add(array $language): ?Language
    {
        try {
            /** @noinspection PhpUndefinedMethodInspection */
            $language = Language::create($language);
            Cache::tags([Language::class])->flush();

            return $language;
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [
                'trace' => $e->getTrace(),
                'language' => $language,
            ]);
            return null;
        }
    }

    public function update(Language $language, array $fields): bool
    {
        $result = $language->update($fields);
        if ($result) {
            Cache::tags([Construction::class])->flush();
        }
        return $result;
    }

    public function delete(Language $language): bool
    {
        $implementations = $language->constructionImpls()->get();
        $result = $language->delete();
        if ($result) {
            $implementations->unsearchable();
            Cache::tags([Construction::class, Language::class])->flush();
        }

        return (bool) $result;
    }

}
