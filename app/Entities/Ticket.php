<?php

namespace App\Entities;

use App\Enums\TicketStatus;
use App\Enums\TicketType;
use App\ValueObjects\Price;

class Ticket
{
    private Price $price;
    private TicketStatus $status;

    public function noOneBoughtTicket(): bool
    {
        return $this->status->name === TicketStatus::free->name;
    }

    public function getTicketType(): TicketType
    {
        return $this->price->getTicketType();
    }
}
