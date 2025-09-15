<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transport extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'capacity',
        'driver_name',
        'driver_phone',
        'vehicle_number',
        'status',
        'description',
    ];

    protected $casts = [
        'capacity' => 'integer',
    ];

    public function routes(): HasMany
    {
        return $this->hasMany(TransportRoute::class);
    }

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
