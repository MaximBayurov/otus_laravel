<?php

namespace App\Events\CacheHelper;

use App\DTO\CachedMethodData;
use Illuminate\Foundation\Events\Dispatchable;

class AfterPaginationMethodHeat
{
    use Dispatchable;

    /**
     * Create a new event instance.
     */
    public function __construct(
        private readonly CachedMethodData $methodData,
        private readonly int $page
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
}
