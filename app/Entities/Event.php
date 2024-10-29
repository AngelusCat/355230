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

    public function __construct(int $id, string $name, string $description, Carbon $start, Carbon $end, Collection $tickets, Collection $prices, Collection $orders)
    {
        $this->id = $id;
        $this->name = $name;
        $this->description = $description;
        $this->start = $start;
        $this->end = $end;
        $this->tickets = $tickets;
        $this->prices = $prices;
        $this->orders = $orders;
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

    public function getTicketsUserWantsToBuy(Collection $typesOfTicketsAndTheirQuantity)
    {
        $test = $this->tickets->mapToGroups(function (Ticket $ticket) {
            return [$ticket->getTicketType()->value => $ticket];
        });
        $typesOfTicketsAndTheirQuantity->each(function (int $quantity, string $type) use ($test) {
            if ($test->has($type)) {
                dump($test->get($type));
            }
        });
        return $test;
    }
}
