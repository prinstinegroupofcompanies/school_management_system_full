<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AcademicPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'is_current',
        'academic_year'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean'
    ];

    /**
     * Get the current academic period
     */
    public static function current()
    {
        return static::where('is_current', true)->first();
    }

    /**
     * Get all periods for a specific academic year
     */
    public static function forYear($year)
    {
        return static::where('academic_year', $year)->orderBy('name')->get();
    }

    /**
     * Check if a date falls within this period
     */
    public function containsDate($date)
    {
        $date = Carbon::parse($date);
        return $date->between($this->start_date, $this->end_date);
    }

    /**
     * Get the next period
     */
    public function next()
    {
        return static::where('academic_year', $this->academic_year)
            ->where('name', '>', $this->name)
            ->orderBy('name')
            ->first();
    }

    /**
     * Get the previous period
     */
    public function previous()
    {
        return static::where('academic_year', $this->academic_year)
            ->where('name', '<', $this->name)
            ->orderBy('name', 'desc')
            ->first();
    }

    /**
     * Scope for current academic year
     */
    public function scopeCurrentYear($query)
    {
        $currentYear = date('Y');
        return $query->where('academic_year', $currentYear . '/' . ($currentYear + 1));
    }

    /**
     * Get formatted period name
     */
    public function getFormattedNameAttribute()
    {
        return $this->name . ' Period';
    }
}
