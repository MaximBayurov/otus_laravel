<?php

namespace App\Attributes;

use App\Enums\CachedMethodTypesEnum;
use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class CachedMethod
{
    public function __construct(
        public string $key,
        public CachedMethodTypesEnum $type = CachedMethodTypesEnum::SIMPLE,
        public ?string $model = null
    ) {
    }
}
