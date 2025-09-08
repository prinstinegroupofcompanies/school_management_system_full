<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class StudentGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'description', 'color', 'icon', 'display_order',
        'is_active', 'created_by', 'notes'
    ];

    protected $casts = [
        'display_order' => 'integer', 'is_active' => 'boolean'
    ];

    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByDisplayOrder($query)
    {
        return $query->orderBy('display_order', 'asc');
    }

    public function scopeByName($query, $name)
    {
        return $query->where('name', 'like', "%{$name}%");
    }

    public function scopeByCode($query, $code)
    {
        return $query->where('code', $code);
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->is_active;
    }

    public function getTotalStudentsAttribute(): int
    {
        return $this->students()->count();
    }

    public function getActiveStudentsAttribute(): int
    {
        return $this->students()->where('is_active', true)->count();
    }

    function getInactiveStudentsAttribute(): int
    {
        return $this->students()->where('is_active', false)->count();
    }

    public function getColorDisplayAttribute(): string
    {
        return $this->color ?: '#6c757d';
    }

    public function getIconDisplayAttribute(): string
    {
        return $this->icon ?: 'fas fa-layer-group';
    }

    public function getNotesDisplayAttribute(): string
    {
        return $this->notes ?: 'No additional notes';
    }

    public function getCreatedByDisplayAttribute(): string
    {
        if (!$this->created_by) return 'System';
        return 'Unknown'; // You can add relationship if needed
    }

    public function canBeEdited(): bool
    {
        return $this->total_students === 0;
    }

    public function canBeDeleted(): bool
    {
        return $this->total_students === 0;
    }

    public function canBeActivated(): bool
    {
        return !$this->is_active;
    }

    public function canBeDeactivated(): bool
    {
        return $this->is_active && $this->total_students === 0;
    }

    public function activate(): void
    {
        if ($this->can_be_activated) {
            $this->is_active = true;
            $this->save();
        }
    }

    public function deactivate(): void
    {
        if ($this->can_be_deactivated) {
            $this->is_active = false;
            $this->save();
        }
    }

    public function updateDisplayOrder(int $order): void
    {
        $this->display_order = $order;
        $this->save();
    }

    public function addNote(string $note): void
    {
        $this->notes = ($this->notes ? $this->notes . "\n" : '') . $note;
        $this->save();
    }
}
