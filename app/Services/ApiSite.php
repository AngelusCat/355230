<?php

namespace App\Services;

use App\Entities\Order;
use Illuminate\Support\Facades\DB;

class ApiSite
{
    public function isOrderBarcodeValid(int $barcode): string
    {
        if (DB::table('orders')->where('barcode', $barcode)->exists()) {
            return json_encode([
                'error' => 'Barcode уже присвоин другому заказу.'
            ]);
        }
        return json_encode([
            'message' => 'Barcode свободен.'
        ]);
    }

    public function isItPossibleToOrder(int $eventId, Order $order): string
    {
        if (!DB::table('events')->where('id', $eventId)->exists()) {
            return json_encode([
                'error' => "Событие с ID: $eventId не существует."
            ]);
        }
        $idsOfSoldOutTickets = collect([]);
        $order->getPurchasedTicketsIds()->each(function ($purchasedTicketsId) use (&$idsOfSoldOutTickets) {
            if (DB::table('purchased_tickets')->where('id', $purchasedTicketsId)->exists()) {
                $idsOfSoldOutTickets->push($purchasedTicketsId);
            }
        });
        if ($idsOfSoldOutTickets->isNotEmpty()) {
            return json_encode([
                'error' => "Выбранные для покупки билеты уже куплены другим пользователем.",
                'idsOfSoldOutTickets' => $idsOfSoldOutTickets->all()
            ]);
        }
        return json_encode([
            "message" => "Заказ возможно выполнить."
        ]);
    }
}
