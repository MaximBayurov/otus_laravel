<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class CachedForModelMethod
{
    public function __construct(public string $model)
    {
    }
}
