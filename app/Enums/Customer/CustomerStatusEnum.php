<?php

namespace App\Enums\Customer;

enum CustomerStatusEnum:int {
    case Pending = 0;
    case Approved = 1;
    case Blacklisted = 2;
}
