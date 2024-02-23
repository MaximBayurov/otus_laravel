<?php

namespace App\Events\CacheHelper;

use App\DTO\CachedMethodData;
use App\Enums\PageSizesEnum;
use Illuminate\Foundation\Events\Dispatchable;

class AfterPaginationMethodHeat
{
    use Dispatchable;

    /**
     * Create a new event instance.
     */
    public function __construct(
        private readonly CachedMethodData $methodData,
        private readonly int $page,
        private readonly PageSizesEnum $pageSize,
    ) {
    }

    /**
     * @return \App\DTO\CachedMethodData
     */
    public function getMethodData(): CachedMethodData
    {
        return $this->methodData;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    public function getPageSize(): PageSizesEnum
    {
        return $this->pageSize;
    }
}
