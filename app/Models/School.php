<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'description',
        'established_year',
        'registration_number',
        'principal_name',
        'principal_phone',
        'principal_email',
        'status',
    ];

    protected $casts = [
        'established_year' => 'integer',
        'status' => 'string',
    ];
}