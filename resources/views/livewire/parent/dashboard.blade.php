@php($children = auth()->user()->children ?? collect())
@forelse($children as $child)
    <div class="card bg-white p-4 rounded shadow mb-4">
        <h3 class="text-lg font-semibold mb-2">{{ $child->full_name }} - {{ $child->classRoom->name ?? 'N/A' }}</h3>
        <div class="space-x-2">
            <a href="{{ route('admission.letter', $child) }}" class="px-3 py-1 bg-blue-600 text-white rounded">Admission Letter</a>
        </div>
    </div>
@empty
    <p>No linked children.</p>
@endforelse

