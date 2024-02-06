<?php

namespace App\Console\Commands;

use App\DTO\CachedMethodData;
use App\Events\CacheHelper\CacheCleanedByKey;
use App\Events\CacheHelper\AfterModelMethodHeat;
use App\Events\CacheHelper\AfterPaginationMethodHeat;
use App\Events\CacheHelper\BeforeModelMethodHeat;
use App\Events\CacheHelper\BeforePaginationMethodHeat;
use App\Events\CacheHelper\BeforeSimpleMethodHeat;
use App\Services\CacheHelper;
use Cache;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Event;
use ReflectionMethod;

class HeatRepositoriesCache extends Command
{
    public function __construct(private readonly CacheHelper $cacheHelper)
    {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:heat-repo-cache
                                {list? : Отображает список прогреваемого кэша}
                                {--A|--all : Прогреть весь кеш}
                                {--C|--clear : Очищать кэш перед прогревом}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Прогревает кэш репозиториев в приложении";

    /**
     * Execute the console command.
     *
     * @throws \ReflectionException
     */
    public function handle()
    {
        $this->registerListeners();

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
        $tableData = [];
        foreach ($this->cacheHelper->getCachedMethodsData() as $cachedMethodData) {
            $tableData[] = [
                'method' => $this->getMethodNameFormatted($cachedMethodData->getMethod()),
                'type' => $cachedMethodData->getType()->value,
                'model' => $cachedMethodData->getModel(),
            ];
        }

        $this->table(
            ['Кэшируемый метод', 'Тип', 'Модель'],
            $tableData
        );
    }

    /**
     * Прогревает весь кэш
     * @return void
     * @throws \ReflectionException
     */
    private function heatAllCache(): void
    {
        foreach ($this->cacheHelper->getCachedMethodsData() as $methodData) {
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
        $cachedMethodsData = $this->cacheHelper->getCachedMethodsData();
        $methods = [];
        foreach ($cachedMethodsData as $methodData) {
            $methods[] = $this->getMethodNameFormatted($methodData->getMethod());
        }
        $methodForHeat = $this->choice(
            "Выберите метод, который нужно прогреть.",
            $methods,
            0
        );

        $this->heatCacheForMethod($cachedMethodsData[array_search($methodForHeat, $methods)]);
    }

    /**
     * Прогревает кэш метода в зависимости от его типа
     * @param CachedMethodData $methodData
     *
     * @return void
     */
    private function heatCacheForMethod(CachedMethodData $methodData): void
    {
        $methodNameFormatted = $this->getMethodNameFormatted($methodData->getMethod());
        $this->alert(sprintf(
            'Прогрев кэша для метода - %s',
            $methodNameFormatted
        ));
        $this->cacheHelper->heat($methodData);
        $this->line('');
        $this->question(sprintf(
            'Завершён прогрев кэша для метода - %s',
            $methodNameFormatted
        ));
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

    /**
     * Очищает кэш по ключу
     * @param array $key
     *
     * @return void
     */
    private function cleanCacheByKey(array $key): void
    {
        if ($this->option('clear')) {
            $this->cacheHelper->cleanByKey($key);
        }
    }

    /**
     * Регистрирует обработчики событий
     * @return void
     */
    private function registerListeners(): void
    {
        Event::listen(function (BeforeModelMethodHeat $event) {
            $this->cleanCacheByKey([
                $this->getMethodNameFormatted($event->getMethodData()->getMethod()),
                $event->getModel()->id
            ]);
        });
        Event::listen(function (AfterModelMethodHeat $event) {
            $this->info(sprintf(
                "Прогрет кэш для модели с id - %s",
                $event->getModel()->id
            ));
        });
        Event::listen(function (BeforePaginationMethodHeat $event) {
            $this->cleanCacheByKey([
                $this->getMethodNameFormatted($event->getMethodData()->getMethod()),
                $event->getPage()
            ]);
        });
        Event::listen(function (AfterPaginationMethodHeat $event) {
            $this->info(sprintf(
                "Прогрет кэш для страницы %s",
                $event->getPage()
            ));
        });
        Event::listen(function (BeforeSimpleMethodHeat $event) {
            $this->cleanCacheByKey([
                $this->getMethodNameFormatted($event->getMethodData()->getMethod()),
            ]);
        });
        Event::listen(function (CacheCleanedByKey $event) {
            $this->info(sprintf("Очищен кэш с ключом %s", $event->getSerializedKey()));
        });
    }
}
