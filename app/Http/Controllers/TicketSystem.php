<?php

namespace App\Http\Controllers;

use App\Entities\Order;
use App\Entities\User;
use App\Enums\TicketType;
use App\Services\EventMapper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
    public function makePurchaseOfTickets(Request $request, int $eventId): void
    {
        $typesOfTicketsAndTheirQuantity = collect([]);
        foreach ($request->all() as $type => $quantity) {
            if (!in_array($type, TicketType::toArray())) {
                continue;
            } else {
                $typesOfTicketsAndTheirQuantity->put($type, $quantity);
            }
        }
        $event = $this->eventMapper->getEvent($eventId);
        $user = new User(1);
        $purchasedTickets = $event->getTicketsUserWantsToBuy($typesOfTicketsAndTheirQuantity);
        $order = new Order($user, $purchasedTickets);
        try {
            DB::beginTransaction();
            $event->makePurchaseOfTickets($order);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
