<?php

namespace App\Events\CacheHelper;

use App\DTO\CachedMethodData;
use App\Models\BaseModel;
use Illuminate\Foundation\Events\Dispatchable;

class CacheCleanedByKey
{
    use Dispatchable;

    /**
     * Create a new event instance.
     */
    public function __construct(
        private readonly array $key,
        private readonly string $serializedKey
    ) {
    }

    /**
     * @return array
     */
    public function getKey(): array
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getSerializedKey(): string
    {
        return $this->serializedKey;
    }
}
