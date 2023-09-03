<?php

namespace App\Enums\Customer;

enum CustomerTypeEnum:int {
    case Cash = 0;
    case Credit = 1;
}
