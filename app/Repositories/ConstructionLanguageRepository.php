<?php

namespace App\Repositories;

use App\Attributes\CachedMethod;
use App\Enums\CachedMethodTypesEnum;
use App\Services\CacheHelper;
use Cache;
use Domain\ModuleLanguageConstructions\Models\Construction;
use Domain\ModuleLanguageConstructions\Models\Language;
use Domain\ModuleLanguageConstructions\Repositories\ConstructionLanguageRepository as IConstructionLanguageRepository;

readonly class ConstructionLanguageRepository implements IConstructionLanguageRepository
{

    public function __construct(
        private CacheHelper $cacheHelper,
    ) {
    }

    #[CachedMethod(
        type: CachedMethodTypesEnum::FOR_MODEL,
        model: Construction::class
    )]
    /**
     * Возвращает массив отформатированных языков программирования для языковой конструкции
     *
     * @param \Domain\ModuleLanguageConstructions\Models\Construction $construction
     *
     * @return array
     */
    public function collectLanguagesFormattedFor(Construction $construction): array
    {
        return Cache::tags([Construction::class, \App\Models\Language::class])->rememberForever(
            $this->cacheHelper->makeKey([__METHOD__, $construction->getId()]),
            function () use ($construction) {
                return array_map(function ($item) {
                    return [
                        'id' => $item['pivot']['language_id'],
                        'code' => $item['pivot']['code'],
                    ];
                }, $construction->getLanguages());
            }
        );
    }

    #[CachedMethod(
        type: CachedMethodTypesEnum::FOR_MODEL,
        model: Language::class
    )]
    /**
     * Возвращает массив отформатированных языковых конструкций для языка программирования
     *
     * @param Language $language
     *
     * @return array
     */
    public function collectConstructionsFormattedFor(Language $language): array
    {
        return Cache::tags([Construction::class, Language::class])->rememberForever(
            $this->cacheHelper->makeKey([__METHOD__, $language->getId()]),
            function () use ($language) {
                return array_map(function ($item) {
                    return [
                        'id' => $item['pivot']['construction_id'],
                        'code' => $item['pivot']['code'],
                    ];
                }, $language->getConstructions());
            }
        );
    }

    public function addForConstruction(Construction $construction, array $languages): void
    {
        if (empty($languages)) {
            return;
        }
        foreach ($languages as $language) {
            $construction->languages()->attach($language['id'], [
                'code' => $language['code'],
            ]);
        }
        Cache::tags([Language::class])->flush();
        Cache::tags([Construction::class])->flush();
    }

    public function updateForConstruction(Construction $construction, array $languages): void
    {
        $construction->languages()->detach();
        foreach ($languages as $language) {
            $construction->languages()->attach($language['id'], [
                'code' => $language['code'],
            ]);
        }
        Cache::tags([Language::class])->flush();
        Cache::tags([Construction::class])->flush();
    }

    public function addForLanguage(Language $language, array $constructions): void
    {
        if (empty($constructions)) {
            return;
        }
        foreach ($constructions as $construction) {
            $language->constructions()->attach($construction['id'], [
                'code' => $construction['code'],
            ]);
        }
        Cache::tags([Language::class])->flush();
        Cache::tags([Construction::class])->flush();
    }

    public function updateForLanguage(Language $language, array $constructions): void
    {
        $language->constructions()->detach();
        foreach ($constructions as $construction) {
            $language->constructions()->attach($construction['id'], [
                'code' => $construction['code'],
            ]);
        }
        Cache::tags([Language::class])->flush();
        Cache::tags([Construction::class])->flush();
    }
}
