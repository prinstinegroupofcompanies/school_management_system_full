<?php

namespace App\Livewire\SuperAdmin;

use App\Models\User;
use App\Notifications\AccountCreated;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Livewire\Component;

class UserCreate extends Component
{
    public string $name = '';
    public string $email = '';
    public string $username = '';
    public array $roles = [];
    public array $permissions = [];

    public function mount(): void
    {
        $this->username = '';
    }

    public function updatedName(): void
    {
        if ($this->name) {
            $first = Str::slug(collect(explode(' ', trim($this->name)))->first());
            $prefix = \Illuminate\Support\Str::lower(config('school.short_name', 'school'));
            $this->username = $prefix . '.' . $first . '.' . date('Y');
        }
    }

    public function createUser(): void
    {
        $this->validate([
            'name' => 'required|string|min:2',
            'email' => 'required|email|unique:users,email',
            'roles' => 'required|array|min:1',
        ]);

        $prefix = Str::lower(config('school.short_name', 'school'));
        $baseUsername = $this->username ?: ($prefix . '.' . Str::slug(explode(' ', $this->name)[0]) . '.' . date('Y'));
        $username = $baseUsername;
        $i = 1;
        while (User::where('username', $username)->exists()) {
            $username = $baseUsername . '.' . $i++;
        }

        $rawPassword = Str::random(10);

        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'username' => $username,
            'password' => bcrypt($rawPassword),
            'is_active' => true,
            'user_type' => 'staff',
            'must_change_password' => true,
        ]);

        if (!empty($this->roles)) {
            $user->assignRole($this->roles);
        }
        if (!empty($this->permissions)) {
            $user->givePermissionTo($this->permissions);
        }

        Notification::send($user, new AccountCreated($username, $rawPassword));

        $this->dispatch('user-created');
        $this->reset(['name', 'email', 'username', 'roles', 'permissions']);
    }

    public function render()
    {
        return view('livewire.super-admin.user-create');
    }
}


