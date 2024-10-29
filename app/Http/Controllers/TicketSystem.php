<?php

namespace App\Http\Controllers;

use App\Enums\TicketType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketSystem extends Controller
{
    public function index(): View
    {
        $event = new \App\Entities\Event(1,"Test", "Test", new Carbon(now()), new Carbon(now()));
        $priceAdult = new \App\ValueObjects\Price(\App\Enums\TicketType::adult, 100);
        $priceChildren = new \App\ValueObjects\Price(TicketType::kid, 100);
        $ticket1 = new \App\Entities\Ticket($priceAdult, \App\Enums\TicketStatus::free);
        $ticket2 = new \App\Entities\Ticket($priceChildren, \App\Enums\TicketStatus::free);
        $prices = collect([$priceAdult, $priceChildren]);
        $tickets = collect([$ticket1, $ticket2]);
        $event->setPrices($prices);
        $event->setTickets($tickets);
        return view("index", ["event" => $event, "tickets" => $event->getNumberOfFreeTicketsOfEachType()]);
    }
    public function makePurchaseOfTickets(Request $request, int $eventId)
    {
        //
    }
}
