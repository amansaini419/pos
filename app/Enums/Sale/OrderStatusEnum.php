<?php

namespace App\Enums\Sale;

enum OrderStatusEnum:int {
    case Pending = 0;
    case Rejected = 1;
    case Approved = 2;
    case Delivering = 3;
    case Delivered = 4;
}
