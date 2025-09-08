@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">User Details</h1>
                <p class="text-gray-600 mt-2">View user information and profile</p>
            </div>
            <a href="{{ route('users.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>Back to Users
            </a>
        </div>

        <!-- User Details -->
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Basic Information</h3>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Full Name:</span>
                        <p class="text-gray-900">{{ $user->name }}</p>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Email Address:</span>
                        <p class="text-gray-900">{{ $user->email }}</p>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">User Type:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($user->user_type === 'admin') bg-purple-100 text-purple-800
                            @elseif($user->user_type === 'teacher') bg-blue-100 text-blue-800
                            @elseif($user->user_type === 'student') bg-green-100 text-green-800
                            @elseif($user->user_type === 'finance') bg-yellow-100 text-yellow-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($user->user_type) }}
                        </span>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Status:</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($user->status === 'active') bg-green-100 text-green-800
                            @elseif($user->status === 'inactive') bg-red-100 text-red-800
                            @else bg-gray-100 text-gray-800 @endif">
                            {{ ucfirst($user->status) }}
                        </span>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h3>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Phone:</span>
                        <p class="text-gray-900">{{ $user->phone ?? 'Not provided' }}</p>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Address:</span>
                        <p class="text-gray-900">{{ $user->address ?? 'Not provided' }}</p>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">City:</span>
                        <p class="text-gray-900">{{ $user->city ?? 'Not provided' }}</p>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Country:</span>
                        <p class="text-gray-900">{{ $user->country ?? 'Not provided' }}</p>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Created At:</span>
                        <p class="text-gray-900">{{ $user->created_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Last Updated:</span>
                        <p class="text-gray-900">{{ $user->updated_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                    
                    @if($user->last_login_at)
                    <div>
                        <span class="text-sm font-medium text-gray-500">Last Login:</span>
                        <p class="text-gray-900">{{ $user->last_login_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                    @endif
                    
                    <div>
                        <span class="text-sm font-medium text-gray-500">Email Verified:</span>
                        <p class="text-gray-900">{{ $user->email_verified_at ? 'Yes' : 'No' }}</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 pt-6 border-t border-gray-200 flex items-center justify-end space-x-4">
                <a href="{{ route('users.edit', $user) }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit User
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
