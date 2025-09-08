@extends('layouts.app')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="max-w-md w-full bg-white rounded-lg shadow-md p-6 text-center">
        <div class="text-6xl font-bold text-red-500 mb-4">500</div>
        <h1 class="text-2xl font-semibold text-gray-800 mb-2">Server Error</h1>
        <p class="text-gray-600 mb-6">Something went wrong on our end. Please try again later.</p>
        <a href="{{ route('dashboard') }}" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
            Go to Dashboard
        </a>
    </div>
</div>
@endsection
