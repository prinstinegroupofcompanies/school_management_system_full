<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Hostel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'warden_name',
        'warden_phone',
        'capacity',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    public function rooms(): HasMany
    {
        return $this->hasMany(HostelRoom::class);
    }

    public function students(): HasManyThrough
    {
        return $this->hasManyThrough(Student::class, HostelRoom::class);
    }
}
