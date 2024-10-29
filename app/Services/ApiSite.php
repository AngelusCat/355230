<?php

namespace App\Services;

use App\Entities\Order;
use Illuminate\Support\Facades\DB;

class ApiSite
{
    public function isOrderBarcodeValid(int $barcode)
    {
        //
    }

    public function isItPossibleToOrder(int $eventId, Order $order): string
    {
        if (!DB::table('events')->where('id', $eventId)->exists()) {
            return json_encode([
                'error' => "Событие с ID: $eventId не существует."
            ]);
        }
        return json_encode([
            "message" => "Заказ возможно выполнить."
        ]);
    }
}
