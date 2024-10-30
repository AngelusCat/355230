<?php

namespace App\Services;

class Barcode
{
    /**
     * Генерирует barcode для любой части системы (будь то заказ или билет)
     * @return int barcode
     */
    public function generateBarcode(): int
    {
        return mt_rand();
    }
}
