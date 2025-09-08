<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeItem extends Model
{
    use HasFactory;

    protected $table = 'fee_items';

    protected $fillable = [
        'item_name',
        'quantity',
        'price_per_unit',
        'total',
        'class_id',
        'semester',
        'year',
        'is_active',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'price_per_unit' => 'decimal:2',
        'total' => 'decimal:2',
        'is_active' => 'boolean',
        'year' => 'integer',
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }
}


