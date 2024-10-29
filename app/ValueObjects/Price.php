<?php

namespace App\ValueObjects;

use App\Enums\TicketType;

class Price
{
    private TicketType $ticketType;
    private int $price;

    public function __construct(TicketType $ticketType, int $price)
    {
        $this->ticketType = $ticketType;
        $this->price = $price;
    }

    public function getTicketType(): TicketType
    {
        return $this->ticketType;
    }
}
