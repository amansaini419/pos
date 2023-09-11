<?php

namespace App\Enums\Warehouse;

enum StockRequestEnum:int {
    case Pending = 0;
    case Rejected = 1;
    case Approved = 2;
}
