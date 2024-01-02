<?php

namespace App\Helpers;

class ModelHelper
{
    public static function getNameFormatted(string $modelClass): string
    {
        $modelPieces = explode('\\', $modelClass);
        return end($modelPieces);
    }
}
