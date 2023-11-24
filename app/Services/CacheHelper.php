<?php

namespace App\Services;

class CacheHelper
{
    /**
     * Возвращает ключ кэша по переданным параметрам
     * @param array $params
     *
     * @return string
     */
    public function makeKey(array $params): string
    {
        return md5(serialize(array_unique($params)));
    }
}
