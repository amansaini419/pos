<?php

namespace App\Models;

use App\Enums\Inventory\InventoryTypeEnum;
use App\Enums\Warehouse\StockRequestStatusEnum;
use App\Enums\Warehouse\WarehouseTypeEnum;
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

    public function warehouse(): MorphOne{
        return $this->morphOne(Warehouse::class, 'relatable');
    }

    public static function boot() {
        parent::boot();

        static::updated(function($item) {
            if($item->request_status === StockRequestStatusEnum::Approved->value){
                if($item->inventory){
                    $item->inventory()->update([
                        'product_id' => $item->product_id,
                        'quantity' => -$item->quantity,
                    ]);
                }
                else{
                    $item->inventory()->create([
                        'inventory_type' => InventoryTypeEnum::Removed->value,
                        'product_id' => $item->product_id,
                        'quantity' => -$item->quantity,
                    ]);
                }

                if($item->warehouse){
                    $item->warehouse()->update([
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                    ]);
                }
                else{
                    $item->warehouse()->create([
                        'warehouse_type' => WarehouseTypeEnum::Removed->value,
                        'product_id' => $item->product_id,
                        'quantity' => $item->quantity,
                        'salesagent_id' => $item->requested_by,
                    ]);
                }
            }
        });

        static::deleted(function($item) {
            $item->inventory()->delete();
            $item->warehouse()->delete();
        });
    }
}
