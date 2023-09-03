<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;

    public function customer(): BelongsTo{
        return $this->belongsTo(Customer::class);
    }

    public function saleProducts(): HasMany{
        return $this->hasMany(
            SaleProduct::class,
        );
    }
}
