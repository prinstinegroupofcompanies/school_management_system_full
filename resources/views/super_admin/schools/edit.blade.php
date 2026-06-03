@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-3xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Edit school: {{ $school->name }}</h1>
        <form method="POST" action="{{ route('super_admin.schools.update', $school) }}" class="bg-white shadow rounded-lg p-6 space-y-6">
            @csrf
            @method('PUT')
            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-3">School details</h2>
                <div class="space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">School name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $school->name) }}" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="code" class="block text-sm font-medium text-gray-700">Code</label>
                            <input type="text" name="code" id="code" value="{{ old('code', $school->code) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">School email</label>
                            <input type="email" name="email" id="email" value="{{ old('email', $school->email) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        </div>
                    </div>
                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $school->phone) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                        <textarea name="address" id="address" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">{{ old('address', $school->address) }}</textarea>
                    </div>
                    <div>
                        <label for="website" class="block text-sm font-medium text-gray-700">School website URL</label>
                        <input type="url" name="website" id="website" value="{{ old('website', $school->website) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="https://www.school.com">
                    </div>
                    <div>
                        <label for="login_background_image" class="block text-sm font-medium text-gray-700">Login page background image (URL)</label>
                        <input type="url" name="login_background_image" id="login_background_image" value="{{ old('login_background_image', $school->login_background_image) }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div>
                        <label class="inline-flex items-center">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $school->is_active) ? 'checked' : '' }} class="rounded border-gray-300">
                            <span class="ml-2 text-sm text-gray-700">School is active</span>
                        </label>
                    </div>
                </div>
            </div>
            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-3">Add-ons (features for this school)</h2>
                <p class="text-sm text-gray-500 mb-3">Enable only the features this school should have access to.</p>
                @php $enabledKeys = $school->addons->where('enabled', true)->pluck('feature_key')->toArray(); @endphp
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    @foreach($features as $key => $config)
                    <label class="flex items-start">
                        <input type="checkbox" name="addons[]" value="{{ $key }}" {{ in_array($key, old('addons', $enabledKeys)) ? 'checked' : '' }} class="rounded border-gray-300 mt-1">
                        <span class="ml-2">
                            <span class="font-medium text-gray-900">{{ $config['label'] }}</span>
                            @if(!empty($config['description']))<span class="block text-sm text-gray-500">{{ $config['description'] }}</span>@endif
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="flex justify-end gap-2">
                <a href="{{ route('super_admin.schools.show', $school) }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
