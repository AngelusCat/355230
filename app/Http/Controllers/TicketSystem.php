<?php

namespace App\Http\Controllers;

use App\Entities\User;
use App\Enums\TicketType;
use App\Services\EventMapper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TicketSystem extends Controller
{
    public function __construct(private EventMapper $eventMapper){}
    public function index(): View
    {
        $eventId = 1;
        $event = $this->eventMapper->getEvent($eventId);
        return view("index", ["event" => $event, "tickets" => $event->getNumberOfFreeTicketsOfEachType()]);
    }
    public function makePurchaseOfTickets(Request $request, int $eventId)
    {
        $event = $this->eventMapper->getEvent($eventId);
        dump($event);
        dump($request);
        $user = new User(1);
        dump($event->getTicketsUserWantsToBuy(collect(["adult" => 1, "kid" => 2])));
    }
}
