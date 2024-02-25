<?php

namespace App\Repositories;

use App\Attributes\CachedMethod;
use App\Enums\CachedMethodTypesEnum;
use App\Enums\PageSizesEnum;
use App\Events\CacheHelper\TestEvent;
use App\Services\CacheHelper;
use Cache;
use Domain\ModuleLanguageConstructions\Models\Construction;
use Domain\ModuleLanguageConstructions\Models\Language;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

readonly class ConstructionsRepository implements \Domain\ModuleLanguageConstructions\Repositories\ConstructionsRepository
{
    public function __construct(
        private CacheHelper $cacheHelper
    ) {
    }

    #[CachedMethod]
    /**
     * Возвращает отформатированные опции для select с конструкциями
     *
     * @return array
     */
    public function getOptions(): array
    {
        return Cache::tags([Construction::class])->rememberForever(
            $this->cacheHelper->makeKey([__METHOD__]),
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

    #[CachedMethod(type: CachedMethodTypesEnum::PAGINATION)]
    public function getPagination(int $page, PageSizesEnum $pageSize = PageSizesEnum::SIZE_20): LengthAwarePaginator
    {
        return Cache::tags([Construction::class])->rememberForever(
            $this->cacheHelper->makeKey([__METHOD__, $page, $pageSize->value]),
            function () use ($page, $pageSize) {
                /** @noinspection PhpUndefinedMethodInspection */
                return Construction::paginate(
                    $pageSize->value,
                    pageName: config('pagination.constructions_page_name'),
                    page: $page
                );
            }
        );
    }

    public function getBySlug(string $slug): ?Construction
    {
        return Construction::firstWhere('slug', '=', $slug);
    }

    public function getAll(): Collection
    {
        return Construction::all();
    }

    public function add(array $construction): ?Construction
    {
        try {
            /** @noinspection PhpUndefinedMethodInspection */
            $construction = Construction::create($construction);
            Cache::tags([Construction::class])->flush();

            return $construction;
        } catch (\Exception $e) {
            Log::error($e->getMessage(), [
                'trace' => $e->getTrace(),
                'construction' => $construction,
            ]);
            return null;
        }
    }

    public function update(Construction $construction, array $fields): bool
    {
        $result = $construction->update($fields);
        if ($result) {
            Cache::tags([Construction::class])->flush();
        }

        return $result;
    }

    public function delete(Construction $construction): bool
    {
        $implementations = $construction->languageImpls()->get();
        $result = $construction->delete();
        if ($result) {
            $implementations->unsearchable();
            Cache::tags([Construction::class, Language::class])->flush();
        }

        return (bool) $result;
    }
}
