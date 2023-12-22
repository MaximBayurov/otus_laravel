<?php

namespace App\Console\Commands;

use App\Attributes\CachedForModelMethod;
use App\Attributes\CachedMethod;
use App\Attributes\CachedPaginationMethod;
use App\Enums\CachedMethodTypesEnum;
use App\Models\BaseModel;
use App\Services\CacheHelper;
use App\Services\ConstructionService;
use App\Services\LanguageService;
use App\Services\PaginationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use ReflectionClass;
use ReflectionMethod;
use App\Models;

class HeatServicesCache extends Command
{
    public function __construct(private CacheHelper $cacheHelper)
    {
        parent::__construct();
    }

    const CACHED_SERVICES = [
        ConstructionService::class,
        LanguageService::class,
    ];

    protected static array $cachedMethodsData;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:heat-services-cache
                                {list? : Отображает список прогреваемого кэша}
                                {--A|--all : Прогреть весь кеш}
                                {--C|--clear : Очищать кэш перед прогревом}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Прогревает кэш сервисов в приложении";

    /**
     * Execute the console command.
     *
     * @throws \ReflectionException
     */
    public function handle()
    {
        if ($this->argument('list')) {
            $this->displayList();
            return;
        }

        if ($this->option('no-interaction') || $this->option('all')) {
            $this->heatAllCache();
            return;
        }

        $this->heatCacheByUserInput();
    }

    /**
     * Отображает список прогреваемого кэша
     *
     * @return void
     * @throws \ReflectionException
     */
    private function displayList(): void
    {
        $cachedMethodsData = $this->getCachedMethodsData();
        array_walk($cachedMethodsData, function (&$data) {
            if (is_a($data['type'], CachedMethodTypesEnum::class)) {
                $data['type'] = $data['type']->value;
            }
            if (is_a($data['method'], ReflectionMethod::class)) {
                $data['method'] = $this->getMethodNameFormatted($data['method']);
            }
        });
        $this->table(
            ['Кэшируемый метод', 'Ключ кэша (не шифрованный)', 'Тип', 'Модель'],
            $cachedMethodsData
        );
    }

    /**
     * Прогревает весь кэш
     * @return void
     * @throws \ReflectionException
     */
    private function heatAllCache(): void
    {
        foreach ($this->getCachedMethodsData() as $methodData) {
            $this->heatCacheForMethod($methodData);
        }
    }

    /**
     * Прогревает кэш в зависимости от пользовательского ввода
     *
     * @return void
     * @throws \ReflectionException
     */
    private function heatCacheByUserInput(): void
    {
        $cachedMethodsData = $this->getCachedMethodsData();
        $methods = array_column($cachedMethodsData, 'method');
        array_walk($methods, function(&$method) {
            $method = $this->getMethodNameFormatted($method);
        });
        $methodForHeat = $this->choice(
            "Выберите метод, который нужно прогреть.",
            $methods,
            0
        );

        $this->heatCacheForMethod($cachedMethodsData[array_search($methodForHeat, $methods)]);
    }

    /**
     * Возвращает список кэшированных методов для сервисов
     * @return array
     * @throws \ReflectionException
     */
    private function getCachedMethodsData(): array
    {
        self::$cachedMethodsData = [];
        foreach (self::CACHED_SERVICES as $serviceClass) {
            $methods = (new ReflectionClass($serviceClass))->getMethods(ReflectionMethod::IS_PUBLIC);
            foreach ($methods as $method) {
                $cachedMethodAttr = $method->getAttributes(CachedMethod::class);
                if (count($cachedMethodAttr) > 0) {
                    $data = [
                        'method' => $method,
                        'cacheKey' => reset($cachedMethodAttr)->newInstance()->key,
                        'type' => CachedMethodTypesEnum::SIMPLE,
                    ];

                    if (count($method->getAttributes(CachedPaginationMethod::class)) > 0) {
                        $data['type'] = CachedMethodTypesEnum::PAGINATION;
                    } elseif (count($cachedForModelMethodAttr = $method->getAttributes(CachedForModelMethod::class)) > 0) {
                        $cachedForModelMethodAttr = reset($cachedForModelMethodAttr)->newInstance();
                        if (!is_a($cachedForModelMethodAttr->model, BaseModel::class, true)) {
                            continue;
                        }
                        $data['type'] = CachedMethodTypesEnum::FOR_MODEL;
                        $data['model'] = $cachedForModelMethodAttr->model;
                    }

                    self::$cachedMethodsData[] = $data;
                }
            }
        }
        return self::$cachedMethodsData;
    }

    /**
     * Прогревает кэш метода в зависимости от его типа
     * @param array $methodData
     *
     * @return void
     */
    private function heatCacheForMethod(array $methodData): void
    {
        $this->alert(sprintf(
            'Прогрев кэша для метода - %s',
            $this->getMethodNameFormatted($methodData['method'])
        ));
        switch ($methodData['type']) {
            case CachedMethodTypesEnum::PAGINATION:
                $this->heatForPaginationMethod($methodData);
                break;
            case CachedMethodTypesEnum::FOR_MODEL:
                $this->heatForModelMethod($methodData);
                break;
            case CachedMethodTypesEnum::SIMPLE:
            default:
                $this->heatForSimpleMethod($methodData);
        }
        $this->line('');
        $this->question(sprintf(
            'Завершён прогрев кэша для метода - %s',
            $this->getMethodNameFormatted($methodData['method'])
        ));
    }

    /**
     * Прогревает кэш для методов, кэширующих пагинацию
     * @param $methodData
     *
     * @return void
     */
    private function heatForPaginationMethod($methodData): void
    {
        if (!is_a($methodData['method']->class,PaginationService::class, true)) {
            return;
        }
        $paginationService = app($methodData['method']->class);
        $page = 1;
        do {
            $this->cleanCacheByKey([
                $methodData['cacheKey'],
                $page
            ]);

            $pagination = $paginationService->getPagination($page);
            $this->info(sprintf(
                "Прогрет кэш для страницы %s",
                $page
            ));
            $page++;
        } while ($pagination->count() > 0);
    }

    /**
     * Прогревает кэш для методов, работающих с моделью
     * @param $methodData
     *
     * @return void
     */
    private function heatForModelMethod($methodData): void
    {
        $models = call_user_func([$methodData['model'], "all"]);
        $models->each(function ($model) use ($methodData) {
            $this->cleanCacheByKey([
                $methodData['cacheKey'],
                $model->id
            ]);
            $service = app($methodData['method']->class);
            call_user_func([$service, $methodData['method']->name], $model);
            $this->info(sprintf(
                "Прогрет кэш для модели с id - %s",
                $model->id
            ));
        });
    }

    /**
     * Прогревает кэш для простых кэшированных методов
     * @param $methodData
     *
     * @return void
     */
    private function heatForSimpleMethod($methodData): void
    {
        $this->cleanCacheByKey([$methodData['cacheKey']]);
        $service = app($methodData['method']->class);
        call_user_func([$service, $methodData['method']->name]);
    }

    /**
     * Очищает кэш по ключу
     * @param array $key
     *
     * @return void
     */
    private function cleanCacheByKey(array $key): void
    {
        if ($this->option('clear')) {
            $cacheKey = $this->cacheHelper->makeKey($key);
            Cache::forget($cacheKey);
            $this->info(sprintf("Очищен кэш с ключом %s", $cacheKey));
        }
    }

    /**
     * Возвращает форматированный метод с классом
     * @param \ReflectionMethod $method
     *
     * @return string
     */
    private function getMethodNameFormatted(ReflectionMethod $method): string
    {
        return sprintf(
            "%s::%s",
            $method->class,
            $method->name
        );
    }
}
