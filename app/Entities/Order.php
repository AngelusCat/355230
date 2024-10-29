<?php

namespace App\Entities;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class Order
{
    private User $user;
    private int $totalCost;
    private int $barcode;
    private Carbon $createdAt;
    private Collection $purchasedTickets;
}
