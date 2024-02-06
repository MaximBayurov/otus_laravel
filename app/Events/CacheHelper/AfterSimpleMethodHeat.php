<?php

namespace App\Events\CacheHelper;

use App\DTO\CachedMethodData;
use Illuminate\Foundation\Events\Dispatchable;

class AfterSimpleMethodHeat
{
    use Dispatchable;

    /**
     * Create a new event instance.
     */
    public function __construct(
        private readonly CachedMethodData $methodData
    ) {
    }

    /**
     * @return \App\DTO\CachedMethodData
     */
    public function getMethodData(): CachedMethodData
    {
        return $this->methodData;
    }
}
