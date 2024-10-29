<?php

namespace App\Enums;

enum TicketType: string
{
    case adult = "взрослый";
    case kid = "детский";
    case preferential = "льготный";
    case group = "групповой";
}
