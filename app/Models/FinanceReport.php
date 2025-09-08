<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'range', 'total', 'count', 'pushed_by', 'pushed_at',
    ];
}


