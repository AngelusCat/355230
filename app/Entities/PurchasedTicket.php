<?php

namespace App\Entities;

class PurchasedTicket
{
    private Ticket $ticket;
    private int $barcode;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function getPrice(): int
    {
        return $this->ticket->getPrice();
    }

    public function getTicketId(): int
    {
        return $this->ticket->getId();
    }
}
