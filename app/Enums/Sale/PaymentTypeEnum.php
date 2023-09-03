<?php

namespace App\Enums\Sale;

enum PaymentTypeEnum:int {
    case Cash = 0;
    case Credit = 1;
}
