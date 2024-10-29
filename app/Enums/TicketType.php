<?php

namespace App\Enums;

enum TicketType: string
{
    case adult = "adult";
    case kid = "kid";
    case preferential = "preferential";
    case group = "group";
}
