@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $school->name }}</h1>
                @if($school->code)<span class="text-gray-500">({{ $school->code }})</span>@endif
            </div>
            <div class="flex gap-2">
                <a href="{{ route('super_admin.schools.edit', $school) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">Edit</a>
                <a href="{{ route('super_admin.schools.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 bg-white hover:bg-gray-50">Back to schools</a>
            </div>
        </div>

        @if(session('success'))<div class="mb-4 p-3 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>@endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">School details</h2>
                    <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div><dt class="text-sm text-gray-500">Email</dt><dd class="text-gray-900">{{ $school->email ?? '—' }}</dd></div>
                        <div><dt class="text-sm text-gray-500">Phone</dt><dd class="text-gray-900">{{ $school->phone ?? '—' }}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-sm text-gray-500">Address</dt><dd class="text-gray-900">{{ $school->address ?? '—' }}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-sm text-gray-500">Website</dt><dd class="text-gray-900">@if($school->website)<a href="{{ $school->website }}" target="_blank" rel="noopener" class="text-blue-600 hover:underline">{{ $school->website }}</a>@else—@endif</dd></div>
                        <div><dt class="text-sm text-gray-500">Status</dt><dd><span class="inline-flex px-2 py-1 text-xs font-medium rounded-full {{ $school->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">{{ $school->is_active ? 'Active' : 'Inactive' }}</span></dd></div>
                    </dl>
                </div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Users &amp; activities</h2>
                    <p class="text-sm text-gray-500 mb-4">All users belonging to this school. School admin can add more users and assign roles.</p>
                    @if($school->users->count() > 0)
                    <ul class="divide-y divide-gray-200">
                        @foreach($school->users as $u)
                        <li class="py-2 flex justify-between items-center">
                            <div>
                                <span class="font-medium text-gray-900">{{ $u->name }}</span>
                                <span class="text-gray-500 text-sm ml-2">({{ $u->email }})</span>
                                <span class="inline-flex ml-2 px-2 py-0.5 text-xs rounded bg-gray-100 text-gray-700">{{ $u->user_type }}</span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                    @else
                    <p class="text-gray-500">No users yet.</p>
                    @endif
                </div>
            </div>
            <div>
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Enabled add-ons</h2>
                    <p class="text-sm text-gray-500 mb-3">Features this school can use.</p>
                    <ul class="space-y-2">
                        @php $enabled = $school->addons->where('enabled', true)->pluck('feature_key')->toArray(); @endphp
                        @forelse($enabled as $key)
                            <li class="text-sm text-gray-900">{{ $features[$key]['label'] ?? $key }}</li>
                        @empty
                            <li class="text-sm text-gray-500">No add-ons enabled. <a href="{{ route('super_admin.schools.edit', $school) }}" class="text-purple-600">Edit school</a> to enable.</li>
                        @endforelse
                    </ul>
                </div>
                <div class="bg-white rounded-lg shadow p-6 mt-6">
                    <h2 class="text-lg font-medium text-gray-900 mb-2">School login</h2>
                    @php $adminUser = $school->adminUser(); @endphp
                    @if($adminUser)
                    <p class="text-sm text-gray-500">Admin can log in with:</p>
                    <p class="mt-1 font-medium text-gray-900">{{ $adminUser->email }}</p>
                    @if($school->code)
                    <p class="text-xs text-gray-500 mt-2">School-specific login URL (shows this school’s name and background):</p>
                    <p class="mt-1 text-sm break-all"><a href="{{ url('/login?school=' . $school->code) }}" target="_blank" class="text-blue-600 hover:underline">{{ url('/login?school=' . $school->code) }}</a></p>
                    @endif
                    @if($school->login_background_image)
                    <p class="text-xs text-gray-500 mt-2">Login background image is set.</p>
                    @endif
                    @else
                    <p class="text-sm text-gray-500">No admin user set.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
