<?php

namespace App\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class CachedMethod
{
    public function __construct(
        public string $key
    ) {
    }
}
