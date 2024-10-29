<?php

namespace App\Services;

use App\Entities\Event;
use App\Entities\Ticket;
use App\Enums\TicketStatus;
use App\ValueObjects\Price;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Enums\TicketType;

class EventMapper
{
    public function getEvent(int $eventId): Event
    {

    }

    public function getPrices(int $eventId): Collection
    {
        return collect(DB::table('prices')->where('event_id', $eventId)->get())->map(function ($priceTag) {
            return new Price($priceTag->id, TicketType::from($priceTag->ticket_type), $priceTag->price);
        });
    }

    public function getTickets(int $eventId, Collection $prices): Collection
    {
        return collect(DB::table('tickets')->where('event_id', $eventId)->get())->map(function ($ticket) use ($prices) {
            $price = $prices->first(function ($ticketPrice) use ($ticket) {
                return $ticket->price_id === $ticketPrice->getId();
            });
            return new Ticket($ticket->id, $price, TicketStatus::from($ticket->status));
        });
    }
}
