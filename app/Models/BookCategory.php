<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class BookCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'icon',
        'color',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function books(): HasMany
    {
        return $this->hasMany(Book::class, 'category_id');
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(BookSubcategory::class, 'category_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('is_active', $status);
    }

    public function getTotalBooksAttribute(): int
    {
        return $this->books()->count();
    }

    public function getAvailableBooksAttribute(): int
    {
        return $this->books()->where('available_copies', '>', 0)->count();
    }

    public function getBorrowedBooksAttribute(): int
    {
        return $this->books()->sum('borrowed_copies');
    }

    public function getDigitalBooksAttribute(): int
    {
        return $this->books()->where('is_digital', true)->count();
    }

    public function getPhysicalBooksAttribute(): int
    {
        return $this->books()->where('is_digital', false)->count();
    }

    public function getIconDisplayAttribute(): string
    {
        return $this->icon ?? 'fas fa-book';
    }

    public function getColorDisplayAttribute(): string
    {
        return $this->color ?? '#6c757d';
    }

    public function getStatusDisplayAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->is_active ? 'success' : 'secondary';
    }
}
