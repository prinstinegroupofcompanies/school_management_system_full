<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'isbn',
        'author',
        'publisher',
        'edition',
        'publication_year',
        'description',
        'summary',
        'cover_image',
        'file_path',
        'file_type',
        'file_size',
        'pages',
        'language',
        'category_id',
        'subcategory_id',
        'location',
        'total_copies',
        'available_copies',
        'borrowed_copies',
        'reserved_copies',
        'price',
        'currency',
        'status',
        'is_digital',
        'is_active',
        'tags',
        'views_count',
        'downloads_count',
        'rating',
        'rating_count',
    ];

    protected $casts = [
        'publication_year' => 'integer',
        'file_size' => 'integer',
        'pages' => 'integer',
        'total_copies' => 'integer',
        'available_copies' => 'integer',
        'borrowed_copies' => 'integer',
        'reserved_copies' => 'integer',
        'price' => 'decimal:2',
        'is_digital' => 'boolean',
        'is_active' => 'boolean',
        'tags' => 'array',
        'views_count' => 'integer',
        'downloads_count' => 'integer',
        'rating' => 'decimal:2',
        'rating_count' => 'integer',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(BookSubcategory::class, 'subcategory_id');
    }

    public function bookIssues(): HasMany
    {
        return $this->hasMany(BookIssue::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeBySubcategory($query, $subcategoryId)
    {
        return $query->where('subcategory_id', $subcategoryId);
    }

    public function scopeByAuthor($query, $author)
    {
        return $query->where('author', 'like', "%{$author}%");
    }

    public function scopeByPublisher($query, $publisher)
    {
        return $query->where('publisher', 'like', "%{$publisher}%");
    }

    public function scopeByLanguage($query, $language)
    {
        return $query->where('language', $language);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeDigital($query)
    {
        return $query->where('is_digital', true);
    }

    public function scopePhysical($query)
    {
        return $query->where('is_digital', false);
    }

    public function scopeAvailable($query)
    {
        return $query->where('available_copies', '>', 0);
    }

    public function scopeByPriceRange($query, $minPrice, $maxPrice)
    {
        return $query->whereBetween('price', [$minPrice, $maxPrice]);
    }

    public function scopeByPublicationYear($query, $year)
    {
        return $query->where('publication_year', $year);
    }

    public function scopeByTags($query, $tags)
    {
        if (is_array($tags)) {
            foreach ($tags as $tag) {
                $query->whereJsonContains('tags', $tag);
            }
        } else {
            $query->whereJsonContains('tags', $tags);
        }
        return $query;
    }

    public function scopePopular($query)
    {
        return $query->orderBy('views_count', 'desc')
                    ->orderBy('downloads_count', 'desc');
    }

    public function scopeHighlyRated($query)
    {
        return $query->where('rating', '>=', 4.0)
                    ->orderBy('rating', 'desc');
    }

    public function getIsAvailableAttribute(): bool
    {
        return $this->available_copies > 0;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->bookIssues()
            ->where('status', 'issued')
            ->where('due_date', '<', now())
            ->exists();
    }

    public function getAvailabilityStatusAttribute(): string
    {
        if ($this->available_copies > 0) {
            return 'Available';
        } elseif ($this->reserved_copies > 0) {
            return 'Reserved';
        } else {
            return 'Out of Stock';
        }
    }

    public function getAvailabilityColorAttribute(): string
    {
        return match($this->availability_status) {
            'Available' => 'success',
            'Reserved' => 'warning',
            'Out of Stock' => 'danger',
            default => 'secondary'
        };
    }

    public function getFileSizeDisplayAttribute(): string
    {
        if (!$this->file_size) return 'N/A';
        
        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;
        
        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }
        
        return round($size, 2) . ' ' . $units[$unit];
    }

    public function getAverageRatingAttribute(): float
    {
        return $this->rating ?? 0;
    }

    public function getRatingDisplayAttribute(): string
    {
        return number_format($this->average_rating, 1) . '/5.0';
    }

    public function incrementViews(): void
    {
        $this->increment('views_count');
    }

    public function incrementDownloads(): void
    {
        $this->increment('downloads_count');
    }

    public function updateRating(float $newRating): void
    {
        $currentTotal = $this->rating * $this->rating_count;
        $this->rating_count++;
        $this->rating = ($currentTotal + $newRating) / $this->rating_count;
        $this->save();
    }
}
