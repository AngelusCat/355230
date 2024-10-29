<?php

namespace App\Services;

class Barcode
{
    public function generateBarcode(): int
    {
        return mt_rand();
    }
}
