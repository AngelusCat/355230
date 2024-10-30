<?php

use Illuminate\Support\Facades\DB;
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
    /**
     * Т.к. в этих SQL-запросах используются жесткие значения, которые не задаются пользовательским вводом, то здесь не были использованы плейсхолдеры.
     * Этот маршрут существует только для заполнения БД тестовыми данными.
     */
    DB::statement("INSERT INTO events (name, description, start, end) VALUES ('Test', 'Test', '2024-10-29 06:29:07', '2024-10-30 06:29:07')");
    DB::statement("INSERT INTO prices (ticket_type, price, event_id) VALUES ('adult', 200, 1)");
    DB::statement("INSERT INTO prices (ticket_type, price, event_id) VALUES ('kid', 100, 1)");
    DB::statement("INSERT INTO tickets (event_id, price_id, status) VALUES (1, 1, 'free')");
    DB::statement("INSERT INTO tickets (event_id, price_id, status) VALUES (1, 1, 'free')");
    DB::statement("INSERT INTO tickets (event_id, price_id, status) VALUES (1, 2, 'free')");
    DB::statement("INSERT INTO tickets (event_id, price_id, status) VALUES (1, 2, 'free')");
    DB::statement("INSERT INTO users (id) VALUES (1)");
});
