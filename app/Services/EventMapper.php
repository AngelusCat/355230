<?php

namespace App\Services;

use App\Entities\Event;
use App\Entities\Order;
use App\Entities\PurchasedTicket;
use App\Entities\Ticket;
use App\Enums\TicketStatus;
use App\ValueObjects\Price;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Enums\TicketType;

class EventMapper
{
    /**
     * Собирает Event из БД
     * @param int $eventId
     * @return Event
     */
    public function getEvent(int $eventId): Event
    {
        $event = DB::table("events")->where("id", $eventId)->first();
        $prices = $this->getPrices($eventId);
        $tickets = $this->getTickets($eventId, $prices);
        $orders = $this->getLazyOrders();
        return new Event($eventId, $event->name, $event->description, new Carbon($event->start), new Carbon($event->end), $tickets, $prices, $orders);
    }

    /**
     * Сохраняет успешно оформленный заказ в БД
     * @param int $eventId
     * @param Order $order
     * @return int
     */

    public function saveOrder(int $eventId, Order $order): int
    {
        return DB::table("orders")->insertGetId([
            "event_id" => $eventId,
            "user_id" => $order->getUserId(),
            "totalCost" => $order->getTotalCost(),
            "barcode" => $order->getBarcode(),
            "created_at" => $order->getCreatedAt()
        ]);
    }

    /**
     * Сохраняет информацию о купленных билетах
     * @param int $orderId
     * @param Collection $purchasedTickets
     * @return void
     */

    public function savePurchasedTickets(int $orderId, Collection $purchasedTickets): void
    {
        $purchasedTickets->each(function (PurchasedTicket $ticket) use ($orderId) {
            DB::table("purchased_tickets")->insert([
                "ticket_id" => $ticket->getTicketId(),
                "order_id" => $orderId,
                "barcode" => $ticket->getBarcode()
            ]);
        });
        $purchasedTickets->each(function (PurchasedTicket $ticket) {
            DB::table("tickets")->where("id", $ticket->getTicketId())->update([
                "status" => 'purchased'
            ]);
        });
    }

    /**
     * Создает коллекцию объектов Price по информации из БД
     * @param int $eventId
     * @return Collection
     */

    private function getPrices(int $eventId): Collection
    {
        return collect(DB::table('prices')->where('event_id', $eventId)->get())->map(function ($priceTag) {
            return new Price($priceTag->id, TicketType::from($priceTag->ticket_type), $priceTag->price);
        });
    }

    /**
     * Создает коллекцию объектов Ticket по информации из БД
     * @param int $eventId
     * @param Collection $prices
     * @return Collection
     */

    private function getTickets(int $eventId, Collection $prices): Collection
    {
        return collect(DB::table('tickets')->where('event_id', $eventId)->get())->map(function ($ticket) use ($prices) {
            $price = $prices->first(function ($ticketPrice) use ($ticket) {
                return $ticket->price_id === $ticketPrice->getId();
            });
            return new Ticket($ticket->id, $price, TicketStatus::from($ticket->status));
        });
    }

    /**
     * Возвращает пустую коллекцию, потому что используется ленивая загрузка, на данный момент система не нуждается в созданиии объектов этого типа
     * @return Collection
     */

    private function getLazyOrders(): Collection
    {
        return collect([]);
    }

    /**
     * Должен возвращать коллекцию объектов Order по информации из БД, но т.к. в системе нет пока что нужды в этом, реализация этого метода опускается
     * @param int $eventId
     * @return Collection
     */

    public function getOrders(int $eventId): Collection
    {
        //
    }
}
