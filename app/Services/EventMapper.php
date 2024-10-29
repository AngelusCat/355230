<?php

namespace App\Services;

use App\Entities\Event;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EventMapper
{
    public function getEvent(int $eventId): Event
    {

    }

    public function getPrices(int $eventId): Collection
    {
        dump(DB::table('prices')->where('event_id', $eventId)->get());
    }
}
