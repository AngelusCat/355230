<?php

namespace App\Enums;

enum TicketStatus: string
{
    case free = "free";
    case purchased = "purchased";
}
