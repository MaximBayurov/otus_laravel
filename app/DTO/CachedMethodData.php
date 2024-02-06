<?php

namespace App\DTO;

use App\Enums\CachedMethodTypesEnum;
use ReflectionMethod;

readonly class CachedMethodData
{
    public function __construct(
        private ReflectionMethod $method,
        private CachedMethodTypesEnum $type = CachedMethodTypesEnum::SIMPLE,
        private ?string $model = null
    ) {
    }

    /**
     * @return ReflectionMethod
     */
    public function getMethod(): ReflectionMethod
    {
        return $this->method;
    }

    /**
     * @return \App\Enums\CachedMethodTypesEnum
     */
    public function getType(): CachedMethodTypesEnum
    {
        return $this->type;
    }

    /**
     * @return string|null
     */
    public function getModel(): ?string
    {
        return $this->model;
    }
}
