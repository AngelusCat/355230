<?php

namespace App\Entities;

use Illuminate\Support\Collection;

class Event
{
    private string $name;
    private string $description;
    private string $start;
    private string $end;
    private Collection $tickets;
    private Collection $prices;
    private Collection $orders;

    public function getNumberOfFreeTicketsOfEachType(): Collection
    {
        return $this->tickets->filter(function (Ticket $ticket) {
            return $ticket->noOneBoughtTicket() === true;
        })->mapToGroups(function (Ticket $ticket) {
            return [$ticket->getTicketType()->name => $ticket];
        })->map(function (Collection $tickets) {
            return $tickets->count();
        });
    }

    public function setTickets(Collection $tickets): void
    {
        $this->tickets = $tickets;
    }

    public function setPrices(Collection $prices): void
    {
        $this->prices = $prices;
    }
}
