<?php

namespace App\Entities;

use App\Enums\TicketStatus;
use App\ValueObjects\Price;

class Ticket
{
    private Price $price;
    private TicketStatus $status;
}
