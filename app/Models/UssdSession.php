<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UssdSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'session_id', 'phone_number', 'service_code', 'status',
        'current_menu', 'user_input', 'session_data', 'step',
        'last_activity', 'expires_at', 'user_id', 'student_id', 'parent_id'
    ];

    protected $casts = [
        'session_data' => 'array',
        'last_activity' => 'datetime',
        'expires_at' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Guardian::class, 'parent_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'timeout')
                   ->orWhere('expires_at', '<', now());
    }

    public function scopeByPhone($query, $phoneNumber)
    {
        return $query->where('phone_number', $phoneNumber);
    }

    public function scopeByServiceCode($query, $serviceCode)
    {
        return $query->where('service_code', $serviceCode);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'active' => 'green',
            'completed' => 'blue',
            'timeout' => 'orange',
            'cancelled' => 'red',
            default => 'gray'
        };
    }

    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'active' => 'Active',
            'completed' => 'Completed',
            'timeout' => 'Timeout',
            'cancelled' => 'Cancelled',
            default => 'Unknown'
        };
    }

    public function isActive()
    {
        return $this->status === 'active' && 
               ($this->expires_at === null || $this->expires_at > now());
    }

    public function isExpired()
    {
        return $this->status === 'timeout' || 
               ($this->expires_at !== null && $this->expires_at <= now());
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isCancelled()
    {
        return $this->status === 'cancelled';
    }

    public function updateActivity()
    {
        $this->update([
            'last_activity' => now(),
            'expires_at' => now()->addMinutes(5) // USSD sessions typically expire in 5 minutes
        ]);
    }

    public function complete()
    {
        $this->update([
            'status' => 'completed',
            'expires_at' => now()
        ]);
    }

    public function cancel()
    {
        $this->update([
            'status' => 'cancelled',
            'expires_at' => now()
        ]);
    }

    public function timeout()
    {
        $this->update([
            'status' => 'timeout',
            'expires_at' => now()
        ]);
    }

    public function setData($key, $value)
    {
        $data = $this->session_data ?? [];
        $data[$key] = $value;
        $this->update(['session_data' => $data]);
    }

    public function getData($key, $default = null)
    {
        $data = $this->session_data ?? [];
        return $data[$key] ?? $default;
    }

    public function nextStep()
    {
        $this->update(['step' => $this->step + 1]);
    }

    public function setMenu($menu)
    {
        $this->update(['current_menu' => $menu]);
    }

    public function setInput($input)
    {
        $this->update(['user_input' => $input]);
    }

    public static function createSession($sessionId, $phoneNumber, $serviceCode)
    {
        return self::create([
            'session_id' => $sessionId,
            'phone_number' => $phoneNumber,
            'service_code' => $serviceCode,
            'status' => 'active',
            'step' => 1,
            'last_activity' => now(),
            'expires_at' => now()->addMinutes(5)
        ]);
    }

    public static function findActiveSession($sessionId)
    {
        return self::where('session_id', $sessionId)
                  ->where('status', 'active')
                  ->where(function($query) {
                      $query->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                  })
                  ->first();
    }

    public static function cleanupExpiredSessions()
    {
        return self::where('expires_at', '<', now())
                  ->where('status', 'active')
                  ->update(['status' => 'timeout']);
    }
}
