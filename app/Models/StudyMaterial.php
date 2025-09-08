<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudyMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'subject_id',
        'class_id',
        'teacher_id',
        'type',
        'file_path',
        'file_name',
        'file_size',
        'link',
        'tags',
        'status',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'tags' => 'array',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function class(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeForSubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function getFileSizeFormattedAttribute()
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

    public function getTypeIconAttribute()
    {
        return match($this->type) {
            'document' => 'document-text',
            'video' => 'play',
            'link' => 'link',
            'other' => 'document',
            default => 'document'
        };
    }

    public function getTypeColorAttribute()
    {
        return match($this->type) {
            'document' => 'blue',
            'video' => 'red',
            'link' => 'green',
            'other' => 'gray',
            default => 'gray'
        };
    }

    public function isDownloadable()
    {
        return $this->file_path && $this->type === 'document';
    }

    public function getDownloadUrlAttribute()
    {
        if (!$this->isDownloadable()) return null;
        return route('study-materials.download', $this);
    }
}
