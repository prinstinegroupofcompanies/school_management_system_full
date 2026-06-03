<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'dashboard_widgets',
        'theme',
        'filter_preferences',
        'table_preferences',
    ];

    protected $casts = [
        'dashboard_widgets' => 'array',
        'theme' => 'array',
        'filter_preferences' => 'array',
        'table_preferences' => 'array',
    ];

    /**
     * Get the user that owns the settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create settings for a user.
     */
    public static function getOrCreateForUser(int $userId): self
    {
        return static::firstOrCreate(
            ['user_id' => $userId],
            [
                'dashboard_widgets' => ['attendance', 'fees', 'calendar'],
                'theme' => ['dark_mode' => false],
            ]
        );
    }

    /**
     * Update dashboard widgets.
     */
    public function updateWidgets(array $widgets): void
    {
        $this->update(['dashboard_widgets' => $widgets]);
    }

    /**
     * Toggle dark mode.
     */
    public function toggleDarkMode(): void
    {
        $theme = $this->theme ?? ['dark_mode' => false];
        $theme['dark_mode'] = !($theme['dark_mode'] ?? false);
        $this->update(['theme' => $theme]);
    }
}
