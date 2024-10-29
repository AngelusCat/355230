<?php

use App\Enums\TicketType;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    $event = new \App\Entities\Event();
    $priceAdult = new \App\ValueObjects\Price(\App\Enums\TicketType::adult, 100);
    $priceChildren = new \App\ValueObjects\Price(TicketType::kid, 100);
    $ticket1 = new \App\Entities\Ticket($priceAdult, \App\Enums\TicketStatus::free);
    $ticket2 = new \App\Entities\Ticket($priceChildren, \App\Enums\TicketStatus::free);
    $prices = collect([$priceAdult, $priceChildren]);
    $tickets = collect([$ticket1, $ticket2]);
    $event->setPrices($prices);
    $event->setTickets($tickets);
    dump($event->getNumberOfFreeTicketsOfEachType());
});
