<?php

namespace App\Enums;

enum TicketType: string
{
    case adult = "adult";
    case kid = "kid";
    case preferential = "preferential";
    case group = "group";

    public static function toArray(): array
    {
        return [self::adult->name, self::kid->name, self::preferential->name, self::group->name];
    }
}
