<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceOfficerAttendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'finance_officer_id',
        'date',
        'status',
        'remarks',
        'marked_by',
    ];

    public function financeOfficer()
    {
        return $this->belongsTo(FinanceOfficer::class);
    }
}


