<?php

namespace App\Enums\Permissions;

enum Languages
{
    case VIEW;
    case CREATE;
    case UPDATE;
    case DELETE;

    public function code(): string
    {
        return match($this)
        {
            self::VIEW => 'view languages',
            self::CREATE => 'create languages',
            self::UPDATE => 'update languages',
            self::DELETE => 'delete languages',
        };
    }
}
