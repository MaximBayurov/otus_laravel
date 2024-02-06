<?php

namespace App\Events\CacheHelper;

use App\DTO\CachedMethodData;
use App\Models\BaseModel;
use Illuminate\Foundation\Events\Dispatchable;

class BeforeModelMethodHeat
{
    use Dispatchable;

    /**
     * Create a new event instance.
     */
    public function __construct(
        private readonly CachedMethodData $methodData,
        private readonly BaseModel $model
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
     * @return \App\Models\BaseModel
     */
    public function getModel(): BaseModel
    {
        return $this->model;
    }
}
