<?php

namespace App\Services;

use App\Attributes\CachedMethod;
use App\DTO\CachedMethodData;
use App\Enums\CachedMethodTypesEnum;
use App\Enums\PageSizesEnum;
use App\Events\CacheHelper\AfterModelMethodHeat;
use App\Events\CacheHelper\AfterPaginationMethodHeat;
use App\Events\CacheHelper\AfterSimpleMethodHeat;
use App\Events\CacheHelper\BeforeModelMethodHeat;
use App\Events\CacheHelper\BeforePaginationMethodHeat;
use App\Events\CacheHelper\BeforeSimpleMethodHeat;
use App\Events\CacheHelper\CacheCleanedByKey;
use App\Events\CacheHelper\CacheNotCleanedByKey;
use App\Models\BaseModel;
use App\Repositories\ConstructionLanguageRepository;
use App\Repositories\ConstructionsRepository;
use App\Repositories\LanguagesRepository;
use Illuminate\Support\Facades\Cache;
use ReflectionClass;
use ReflectionMethod;

class CacheHelper
{
    const CACHED_REPOSITORIES = [
        LanguagesRepository::class,
        ConstructionsRepository::class,
        ConstructionLanguageRepository::class,
    ];

    /**
     * @var array<CachedMethodData>
     */
    protected static array $cachedMethodsData;

    /**
     * Возвращает ключ кэша по переданным параметрам
     *
     * @param array $params
     *
     * @return string
     */
    public function makeKey(array $params): string
    {
        return md5(serialize(array_unique($params)));
    }

    /**
     * Возвращает список кэшированных методов в репозиториях
     *
     * @return array<CachedMethodData>
     * @throws \ReflectionException
     */
    public function getCachedMethodsData(): array
    {
        self::$cachedMethodsData = [];
        foreach (self::CACHED_REPOSITORIES as $repositoryClass) {
            $methods = (new ReflectionClass($repositoryClass))->getMethods(ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method) {
                $methodData = $this->collectMethodData($method);
                if (!empty($methodData)) {
                    self::$cachedMethodsData[] = $methodData;
                }
            }
        }

        return self::$cachedMethodsData;
    }

    /**
     * @param \ReflectionMethod $method
     *
     * @return CachedMethodData|null
     */
    private function collectMethodData(ReflectionMethod $method): ?CachedMethodData
    {
        $cachedMethodAttr = $method->getAttributes(CachedMethod::class);
        if (count($cachedMethodAttr) <= 0) {
            return null;
        }

        $cachedMethodAttr = reset($cachedMethodAttr)->newInstance();
        if (
            $cachedMethodAttr->type === CachedMethodTypesEnum::FOR_MODEL
            && (
                empty($cachedMethodAttr->model)
                || !is_a($cachedMethodAttr->model, BaseModel::class, true)
            )
        ) {
            return null;
        }

        return new CachedMethodData(
            $method,
            $cachedMethodAttr->type,
            $cachedMethodAttr->model
        );
    }

    public function heat(CachedMethodData $methodData): void
    {
        switch ($methodData->getType()) {
            case CachedMethodTypesEnum::PAGINATION:
                foreach (PageSizesEnum::cases() as $pageSize) {
                    $this->heatForPaginationMethod($methodData, $pageSize);
                }
                break;
            case CachedMethodTypesEnum::FOR_MODEL:
                $this->heatForModelMethod($methodData);
                break;
            case CachedMethodTypesEnum::SIMPLE:
            default:
                $this->heatForSimpleMethod($methodData);
        }
    }

    /**
     * Прогревает кэш для методов, кэширующих пагинацию
     *
     * @param CachedMethodData $methodData
     *
     * @return void
     */
    private function heatForPaginationMethod(CachedMethodData $methodData, PageSizesEnum $pageSize): void
    {
        $repo = app($methodData->getMethod()->class);
        $page = 1;
        do {
            BeforePaginationMethodHeat::dispatch($methodData, $page, $pageSize);
            $pagination = call_user_func([$repo, $methodData->getMethod()->name], $page);
            AfterPaginationMethodHeat::dispatch($methodData, $page, $pageSize);
            $page++;
        } while ($pagination->count() > 0);
    }

    /**
     * Прогревает кэш для методов, работающих с моделью
     *
     * @param CachedMethodData $methodData
     *
     * @return void
     */
    private function heatForModelMethod(CachedMethodData $methodData): void
    {
        $models = call_user_func([$methodData->getModel(), "all"]);
        $models->each(function ($model) use ($methodData) {
            BeforeModelMethodHeat::dispatch($methodData, $model);
            $service = app($methodData->getMethod()->class);
            call_user_func([$service, $methodData->getMethod()->name], $model);
            AfterModelMethodHeat::dispatch($methodData, $model);
        });
    }

    /**
     * Прогревает кэш для простых кэшированных методов
     *
     * @param CachedMethodData $methodData
     *
     * @return void
     */
    private function heatForSimpleMethod(CachedMethodData $methodData): void
    {
        BeforeSimpleMethodHeat::dispatch($methodData);
        $repo = app($methodData->getMethod()->class);
        call_user_func([$repo, $methodData->getMethod()->name]);
        AfterSimpleMethodHeat::dispatch($methodData);
    }

    /**
     * Очищает кэш по ключу
     *
     * @param array $key
     *
     * @return void
     */
    public function cleanByKey(array $key): void
    {
        $cacheKey = $this->makeKey($key);
        Cache::forget($cacheKey);
        CacheCleanedByKey::dispatch($key, $cacheKey);
    }
}
