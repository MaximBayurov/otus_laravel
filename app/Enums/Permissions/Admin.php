<?php

namespace App\Enums\Permissions;

enum Admin
{
    case VIEW;

    public function code(): string
    {
        return match($this)
        {
            self::VIEW => 'view admin',
        };
    }
}
