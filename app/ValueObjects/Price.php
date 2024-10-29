<?php

namespace App\ValueObjects;

use App\Enums\TicketType;

class Price
{
    private int $id;
    private TicketType $ticketType;
    private int $price;

    public function __construct(int $id, TicketType $ticketType, int $price)
    {
        $this->id = $id;
        $this->ticketType = $ticketType;
        $this->price = $price;
    }

    public function getTicketType(): TicketType
    {
        return $this->ticketType;
    }
}
