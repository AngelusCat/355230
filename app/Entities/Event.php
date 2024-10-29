<?php

namespace App\Entities;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class Event
{
    private int $id;
    private string $name;
    private string $description;
    private Carbon $start;
    private Carbon $end;
    private Collection $tickets;
    private Collection $prices;
    private Collection $orders;

    public function getId(): int
    {
        return $this->id;
    }
    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStart(): Carbon
    {
        return $this->start;
    }

    public function getEnd(): Carbon
    {
        return $this->end;
    }

    public function __construct(int $id, string $name, string $description, Carbon $start, Carbon $end)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->start = $start;
        $this->end = $end;
    }

    public function getNumberOfFreeTicketsOfEachType(): Collection
    {
        return $this->tickets->filter(function (Ticket $ticket) {
            return $ticket->noOneBoughtTicket() === true;
        })->mapToGroups(function (Ticket $ticket) {
            return [$ticket->getTicketType()->value => $ticket];
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
