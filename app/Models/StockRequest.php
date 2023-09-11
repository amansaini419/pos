<?php

namespace App\Models;

use App\Enums\Inventory\InventoryTypeEnum;
use App\Enums\Warehouse\StockRequestEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class StockRequest extends Model
{
    use HasFactory;

    public function product(): BelongsTo{
        return $this->belongsTo(Product::class);
    }

    public function requestedBy(): BelongsTo{
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function inventory(): MorphOne{
        return $this->morphOne(Inventory::class, 'relatable');
    }

    public static function boot() {
        parent::boot();

        static::updated(function($item) {
            if($item->request_status === StockRequestEnum::Approved->value){
                $item->inventory()->create([
                    'inventory_type' => InventoryTypeEnum::Removed->value,
                ]);
            }
        });
    }
}
