<?php

namespace App\Entities;

class User
{
    private int $id;
    public function __construct(int $id)
    {
        $this->id = $id;
    }
}
