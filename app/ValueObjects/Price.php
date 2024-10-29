<?php

namespace App\ValueObjects;

use App\Enums\TicketType;

class Price
{
    private TicketType $ticketType;
    private int $price;

    public function getTicketType(): TicketType
    {
        return $this->ticketType;
    }
}
