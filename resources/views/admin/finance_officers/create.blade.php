@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-xl mx-auto bg-white rounded-lg shadow p-6">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-bold text-gray-900">Add Finance Officer</h1>
            <a href="{{ route('admin.finance_officers.index') }}" class="px-3 py-2 bg-gray-600 text-white rounded">Back</a>
        </div>
        <form method="POST" action="{{ route('admin.finance_officers.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <input type="text" name="name" class="mt-1 block w-full border-gray-300 rounded-md" required>
                @error('name')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <input type="email" name="email" class="mt-1 block w-full border-gray-300 rounded-md" required>
                @error('email')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Password</label>
                <input type="password" name="password" class="mt-1 block w-full border-gray-300 rounded-md" required>
                @error('password')<p class="text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Confirm Password</label>
                <input type="password" name="password_confirmation" class="mt-1 block w-full border-gray-300 rounded-md" required>
            </div>
            <div class="pt-2">
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Create Officer</button>
            </div>
        </form>
    </div>
    </div>
</div>
@endsection


