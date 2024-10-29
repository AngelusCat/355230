<?php

namespace App\Entities;

use Illuminate\Support\Collection;

class Event
{
    private string $name;
    private string $description;
    private string $start;
    private string $end;
    private Collection $tickets;
    private Collection $prices;
    private Collection $orders;
}
