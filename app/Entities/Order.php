<?php

namespace App\Entities;

use App\Services\Barcode;
use App\Services\EventMapper;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class Order
{
    private int $id;
    private User $user;
    private int $totalCost;
    private int $barcode;
    private Carbon $createdAt;
    private Collection $purchasedTickets;
    private Barcode $barcodeGenerator;
    private EventMapper $mapper;

    public function __construct(User $user, Collection $purchasedTickets, int $id = 0, int $totalCost = 0, int $barcode = 0, Carbon $createdAt = new Carbon())
    {
        $this->user = $user;
        $this->purchasedTickets = $purchasedTickets;
        $this->totalCost = ($totalCost === 0) ? $this->calculateCostOfOrder() : $totalCost;
        $this->barcode = $barcode;
        $this->createdAt = $createdAt;
        $this->barcodeGenerator = App::make(Barcode::class);
        $this->mapper = App::make(EventMapper::class);
    }

    public function getUserId(): int
    {
        return $this->user->getId();
    }

    public function getTotalCost(): int
    {
        return $this->totalCost;
    }

    public function getBarcode(): int
    {
        return $this->barcode;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    public function setBarcode(int $barcode): void
    {
        $this->barcode = $barcode;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function issueBarcodeToPurchasedTicketsAndSaveThem(): void
    {
        $this->purchasedTickets->each(function (PurchasedTicket $ticket) {
            $ticket->setBarcode($this->barcodeGenerator->generateBarcode());
        });
        $this->purchasedTickets->mapToGroups(function (PurchasedTicket $ticket) {
            return [$ticket->getBarcode() => $ticket];
        })->each(function (Collection $purchasedTickets) {
            if (!$purchasedTickets->containsOneItem()) {
                $purchasedTickets->each(function (PurchasedTicket $ticket) {
                    $ticket->setBarcode($this->barcodeGenerator->generateBarcode());
                });
            }
        });
        $this->mapper->savePurchasedTickets($this->id, $this->purchasedTickets);
    }

    private function calculateCostOfOrder(): int
    {
        $totalCost = 0;
        $this->purchasedTickets->each(function (PurchasedTicket $ticket) use (&$totalCost) {
            $totalCost += $ticket->getPrice();
        });
        return $totalCost;
    }

    public function getPurchasedTicketsIds(): Collection
    {
        return $this->purchasedTickets->map(function (PurchasedTicket $ticket) {
            return $ticket->getTicketId();
        });
    }
}
