<?php

namespace App\Services;

use App\Entities\Event;
use App\ValueObjects\Price;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EventMapper
{
    public function getEvent(int $eventId): Event
    {

    }

    public function getPrices(int $eventId)
    {
        collect(DB::table('prices')->where('event_id', $eventId)->get())->map(function ($priceTag) {
            return new Price();
        });
    }
}
