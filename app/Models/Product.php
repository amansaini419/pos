<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    public function productGroup(): BelongsTo{
        return $this->belongsTo(ProductGroup::class);
    }

    public function purchases(): HasMany{
        return $this->hasMany(Purchase::class);
    }
}
