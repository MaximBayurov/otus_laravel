<?php

namespace App\Enums\Permissions;


enum Constructions
{
    case VIEW;
    case CREATE;
    case UPDATE;
    case DELETE;

    public function code(): string
    {
        return match($this)
        {
            self::VIEW => 'view constructions',
            self::CREATE => 'create constructions',
            self::UPDATE => 'update constructions',
            self::DELETE => 'delete constructions',
        };
    }
}
