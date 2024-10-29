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

    public function getTickets(int $eventId): Collection
    {
        return collect(DB::table('tickets')->where('event_id', $eventId)->get())->map(function ($ticket) {
            return new Ticket($ticket->id, new Price(1,TicketType::kid, 400), TicketStatus::from($ticket->status));
        });
    }
}
