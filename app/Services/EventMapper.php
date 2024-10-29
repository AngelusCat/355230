<?php

namespace App\Services;

use App\Entities\Event;
use App\Entities\Ticket;
use App\Enums\TicketStatus;
use App\ValueObjects\Price;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Enums\TicketType;

class EventMapper
{
    public function getEvent(int $eventId): Event
    {
        $event = DB::table("events")->where("id", $eventId)->first();
        $prices = $this->getPrices($eventId);
        $tickets = $this->getTickets($eventId, $prices);
        $orders = $this->getOrders($eventId);
        return new Event($eventId, $event->name, $event->description, new Carbon($event->start), new Carbon($event->end), $tickets, $prices, $orders);
    }

    private function getPrices(int $eventId): Collection
    {
        return collect(DB::table('prices')->where('event_id', $eventId)->get())->map(function ($priceTag) {
            return new Price($priceTag->id, TicketType::from($priceTag->ticket_type), $priceTag->price);
        });
    }

    private function getTickets(int $eventId, Collection $prices): Collection
    {
        return collect(DB::table('tickets')->where('event_id', $eventId)->get())->map(function ($ticket) use ($prices) {
            $price = $prices->first(function ($ticketPrice) use ($ticket) {
                return $ticket->price_id === $ticketPrice->getId();
            });
            return new Ticket($ticket->id, $price, TicketStatus::from($ticket->status));
        });
    }

    private function getOrders(int $eventId): Collection
    {
        $ordersFromDB = collect(DB::table('orders')->where('event_id', $eventId)->get());
        if ($ordersFromDB->isEmpty()) {
            return $ordersFromDB;
        }
    }
}
