<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceOfficer extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'finance_officer_id', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}


