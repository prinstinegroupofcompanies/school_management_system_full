@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-8">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">My Profile</h1>
            <p class="mt-2 text-gray-600">Finance Officer Profile</p>
        </div>
        <a href="{{ route('finance.dashboard') }}" 
           class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
            Back to Dashboard
        </a>
    </div>

    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center mb-6">
            @if($user->profile_photo)
                <img src="{{ asset('storage/' . $user->profile_photo) }}" 
                     alt="{{ $user->name }}" 
                     class="w-20 h-20 rounded-full object-cover mr-6">
            @else
                <div class="w-20 h-20 bg-gray-300 rounded-full flex items-center justify-center mr-6">
                    <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            @endif
            <div>
                <h2 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h2>
                <p class="text-gray-600">{{ ucfirst($user->user_type) }} Officer</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 mt-2">
                    {{ ucfirst($user->status) }}
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Email</label>
                <p class="text-lg text-gray-900">{{ $user->email }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Phone</label>
                <p class="text-lg text-gray-900">{{ $user->phone ?? 'Not provided' }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Country</label>
                <p class="text-lg text-gray-900">{{ $user->country ?? 'Not provided' }}</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-500 mb-1">Last Login</label>
                <p class="text-lg text-gray-900">
                    {{ $user->last_login_at ? $user->last_login_at->format('M d, Y \a\t g:i A') : 'Never' }}
                </p>
            </div>

            @if($user->address)
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-500 mb-1">Address</label>
                    <p class="text-lg text-gray-900">{{ $user->address }}</p>
                </div>
            @endif
        </div>

        <div class="mt-8 pt-6 border-t border-gray-200">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-lg font-medium text-gray-900">Account Information</h3>
                    <p class="text-sm text-gray-500">Member since {{ $user->created_at->format('M d, Y') }}</p>
                </div>
                <div class="space-x-3">
                    <a href="{{ route('users.profile.edit') }}" 
                       class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md">
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
