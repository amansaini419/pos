<?php

namespace App\Enums\SubAdmin;

enum SubAdminRoleEnum: string{
    case ADMIN = 'admin';
    case SALESAGENT = 'sales_agent';
    case OPERATIONS = 'operations';
    case WAREHOUSE = 'warehouse';
    case FINANCE = 'finance';
}
