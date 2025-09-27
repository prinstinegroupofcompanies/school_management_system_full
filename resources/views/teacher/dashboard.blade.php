@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white shadow rounded-lg p-6">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Teacher Dashboard</h1>
        <div class="text-center py-12">
            <div class="text-gray-400">
                <i class="fas fa-chalkboard-teacher text-6xl mb-4"></i>
                <p class="text-lg">Teacher dashboard is loading...</p>
                <p class="text-sm text-gray-500 mt-2">Please wait while we redirect you to the main dashboard.</p>
            </div>
        </div>
    </div>
</div>

<script>
    // Redirect to main dashboard after a short delay
    setTimeout(function() {
        window.location.href = '{{ route("dashboard") }}';
    }, 2000);
</script>
@endsection
