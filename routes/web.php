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

Route::get('/', [\App\Http\Controllers\TicketSystem::class, 'index'])->name('ticket.index');
Route::post('/{event_id}/buyTickets', [\App\Http\Controllers\TicketSystem::class, 'makePurchaseOfTickets']);

Route::get('/t', function () {
    $mapper = new \App\Services\EventMapper();
    dump($mapper->getPrices(1));
});
