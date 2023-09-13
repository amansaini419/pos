<?php

namespace App\Enums\Warehouse;

enum StockRequestStatusEnum:int {
    case Pending = 0;
    case Rejected = 1;
    case Approved = 2;
}
