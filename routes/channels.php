<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('school', function ($user) {
    if (!$user) {
        return false;
    }
    return [
        'id' => $user->id,
        'name' => $user->name,
        'role' => method_exists($user, 'getRoleNames') ? $user->getRoleNames()->first() : ($user->user_type ?? 'user'),
    ];
});


