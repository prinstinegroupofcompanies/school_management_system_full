<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRecord extends Model
{
    use HasFactory;

    protected $table = 'payment_records';

    protected $fillable = [
        'student_id',
        'fee_id',
        'amount',
        'payment_method',
        'transaction_reference',
        'details',
        'receipt_path',
        'status',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function studentFee()
    {
        return $this->belongsTo(StudentFee::class, 'fee_id');
    }

    public function fee()
    {
        return $this->belongsTo(StudentFee::class, 'fee_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}


