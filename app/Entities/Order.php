<?php

namespace App\Entities;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class Order
{
    private int $id;
    private User $user;
    private int $totalCost;
    private int $barcode;
    private Carbon $createdAt;
    private Collection $purchasedTickets;

    public function __construct(User $user, Collection $purchasedTickets, int $id = 0, int $totalCost = 0, int $barcode = 0, Carbon $createdAt = new Carbon())
    {
        $this->user = $user;
        $this->totalCost = $totalCost;
        $this->barcode = $barcode;
        $this->createdAt = $createdAt;
        $this->purchasedTickets = $purchasedTickets;
    }
}
