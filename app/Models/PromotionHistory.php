<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PromotionHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'from_class_id',
        'to_class_id',
        'term',
        'year',
        'status',
        'average_score',
        'remarks',
        'processed_by',
    ];

    protected $casts = [
        'average_score' => 'decimal:2',
        'year' => 'integer',
    ];

    /**
     * Get the student.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the from class.
     */
    public function fromClass(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'from_class_id');
    }

    /**
     * Get the to class.
     */
    public function toClass(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'to_class_id');
    }

    /**
     * Get the user who processed the promotion.
     */
    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scope for filtering by year.
     */
    public function scopeForYear($query, $year)
    {
        return $query->where('year', $year);
    }

    /**
     * Scope for filtering by status.
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}
