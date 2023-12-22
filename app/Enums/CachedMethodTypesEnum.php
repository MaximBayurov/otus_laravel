<?php

namespace App\Enums;

enum CachedMethodTypesEnum: string
{
    case SIMPLE = 'simple';
    case PAGINATION = 'pagination';
    case FOR_MODEL = 'for_model';
}
