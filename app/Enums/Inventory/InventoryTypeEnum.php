<?php

namespace App\Enums\Inventory;

enum InventoryTypeEnum:int {
    case Added = 0;
    case Removed = 1;
}
