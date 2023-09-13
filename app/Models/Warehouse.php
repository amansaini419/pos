<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Warehouse extends Model
{
    use HasFactory;

    public function relatable(): MorphTo{
        return $this->morphTo();
    }

    public function product(): BelongsTo{
        return $this->belongsTo(Product::class);
    }

    public function salesAgent(): BelongsTo{
        return $this->belongsTo(User::class, 'salesagent_id');
    }
}
