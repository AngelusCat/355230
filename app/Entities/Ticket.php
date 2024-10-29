<?php

namespace App\Entities;

use App\Enums\TicketStatus;
use App\Enums\TicketType;
use App\ValueObjects\Price;

class Ticket
{
    private int $id;
    private Price $price;
    private TicketStatus $status;

    public function __construct(int $id, Price $price, TicketStatus $status)
    {
        $this->id = $id;
        $this->price = $price;
        $this->status = $status;
    }

    public function noOneBoughtTicket(): bool
    {
        return $this->status->name === TicketStatus::free->name;
    }

    public function getTicketType(): TicketType
    {
        return $this->price->getTicketType();
    }

    public function getPrice(): int
    {
        return $this->price->getPrice();
    }
}
