<?php

namespace App\Models;

use App\Enums\Inventory\InventoryTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Purchase extends Model
{
    use HasFactory;

    public function product(): BelongsTo{
        return $this->belongsTo(Product::class);
    }

    public function addedBy(): BelongsTo{
        return $this->belongsTo(User::class, 'added_by');
    }

    public function inventory(): MorphOne{
        return $this->morphOne(Inventory::class, 'relatable');
    }

    public static function boot() {
        parent::boot();

        static::created(function($item) {
            $item->inventory()->create([
                'inventory_type' => InventoryTypeEnum::Added->value,
            ]);
        });

        static::deleted(function($item) {
            $item->inventory()->delete();
        });
    }
}
