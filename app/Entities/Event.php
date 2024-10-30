<?php

namespace App\Entities;

use App\Services\ApiSite;
use App\Services\Barcode;
use App\Services\EventMapper;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

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
    private ApiSite $apiSite;
    private Barcode $barcodeGenerator;
    private EventMapper $mapper;

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
        $this->apiSite = App::make(ApiSite::class);
        $this->barcodeGenerator = App::make(Barcode::class);
        $this->mapper = App::make(EventMapper::class);
    }

    /**
     * @return Collection Коллекция типа "тип билета" => количество билетов определенного типа, свободных для продажи
     */

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

    /**
     * @param Collection $typesOfTicketsAndTheirQuantity Коллекция типа "тип билета" => сколько билетов этого типа хочет купить пользователь
     * @return Collection Коллекция объектов типа PurchasedTicket, ассоциированных с заказом
     */

    public function getTicketsUserWantsToBuy(Collection $typesOfTicketsAndTheirQuantity): Collection
    {
        $ticketsByType = $this->tickets->mapToGroups(function (Ticket $ticket) {
            return [$ticket->getTicketType()->value => $ticket];
        });
        $ticketsUserWantsToBuy = collect([]);
        $typesOfTicketsAndTheirQuantity->each(function (int $quantity, string $type) use ($ticketsUserWantsToBuy, $ticketsByType) {
            if ($ticketsByType->has($type)) {
                $filtered = $ticketsByType->get($type)->filter(function (Ticket $ticket) use ($ticketsUserWantsToBuy) {
                    return $ticket->noOneBoughtTicket() === true;
                });
                for ($i = 1, $a = 0; $i <= $quantity; $i++, $a++) {
                    $ticketsUserWantsToBuy->push(new PurchasedTicket($filtered[$a]));
                }
            }
        });
        return $ticketsUserWantsToBuy;
    }

    /**
     * Проверяет условия возможности оформить заказ (событие существует, выбранные для покупки билеты доступны для продажи)
     * Генерирует уникальный barcode для заказа
     * Сохраняет заказ и информацию о купленных билетах
     * @param Order $order Заказ на покупку билетов, который находится в состоянии оформления
     * @return void
     */

    public function makePurchaseOfTickets(Order $order): void
    {
        $isItPossibleToOrder = json_decode($this->apiSite->isItPossibleToOrder($this->id, $order));

        if (isset($isItPossibleToOrder->message) && $isItPossibleToOrder->message === 'Заказ возможно выполнить.') {
            do {
                $barcode = $this->barcodeGenerator->generateBarcode();
                $isOrderBarcodeValid = json_decode($this->apiSite->isOrderBarcodeValid($barcode));
            } while ((isset($isOrderBarcodeValid->message) && $isOrderBarcodeValid->message === 'Barcode свободен.') === false);
            $order->setBarcode($barcode);
            $order->setId($this->mapper->saveOrder($this->id, $order));
            $order->issueBarcodeToPurchasedTicketsAndSaveThem();
        }
    }

    /**
     * Используется для ленивой загрузки коллекции, с помощью инициализации по требованию
     * @param string $name Название свойства класса Event
     * @return Collection Возвращает коллекцию объектов типа Order, ассоциированных с событием
     */

    public function __get(string $name): Collection
    {
        if ($name !== "orders") {
            throw new \ValueError("Этот метод только для свойства orders (Lazy Load)");
        }
        if ($this->orders->isEmpty()) {
            $this->orders = $this->mapper->getOrders($this->id);
        } else {
            return $this->orders;
        }
    }
}
