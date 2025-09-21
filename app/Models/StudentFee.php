<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentFee extends Model
{
    use HasFactory;

    protected $table = 'student_fees';

    protected $fillable = [
        'student_id',
        'fee_structure_id',
        'class_id',
        'semester',
        'year',
        'academic_year',
        'total_amount',
        'paid_amount',
        'balance',
        'due_date',
        'status',
        'fee_type',
        'description',
        'fee_breakdown',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'due_date' => 'date',
        'fee_breakdown' => 'array',
        'year' => 'integer',
        'academic_year' => 'integer',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function feeStructure()
    {
        return $this->belongsTo(FeeStructure::class, 'fee_structure_id');
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function payments()
    {
        return $this->hasMany(PaymentRecord::class, 'fee_id');
    }
}


