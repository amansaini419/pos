<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    public function assignedTo(): BelongsTo{
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function sales(): HasMany{
        return $this->hasMany(Sale::class);
    }
}
