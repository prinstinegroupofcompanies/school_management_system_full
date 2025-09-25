@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">Signature Settings</h1>
                    <p class="mt-2 text-gray-600">Manage your digital signature for grade sheets and official documents</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.dashboard') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-6" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6" role="alert">
                <strong class="font-bold">Whoops!</strong>
                <span class="block sm:inline">There were some problems with your input.</span>
                <ul class="mt-3 list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Current Signature Display -->
        <div class="bg-white shadow rounded-lg p-6 mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Current Signature</h3>
            
            @if($user->signature)
                <div class="flex items-center space-x-6">
                    <div class="border-2 border-gray-200 rounded-lg p-4">
                        <img src="{{ Storage::url($user->signature) }}" alt="Current Signature" class="h-20 w-40 object-contain">
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">This signature will appear on all grade sheets and official documents.</p>
                        <p class="text-xs text-gray-500 mt-1">Last updated: {{ $user->updated_at->format('M d, Y \a\t g:i A') }}</p>
                    </div>
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No signature uploaded</h3>
                    <p class="mt-1 text-sm text-gray-500">Upload a signature to appear on grade sheets and official documents.</p>
                </div>
            @endif
        </div>

        <!-- Upload New Signature -->
        <div class="bg-white shadow rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Upload New Signature</h3>
            
            <form action="{{ route('admin.settings.signature.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <div>
                    <label for="signature" class="block text-sm font-medium text-gray-700 mb-2">
                        Signature Image
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-gray-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="signature" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                    <span>Upload a file</span>
                                    <input id="signature" name="signature" type="file" class="sr-only" accept="image/*" required>
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 2MB</p>
                        </div>
                    </div>
                    @error('signature')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-md p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800">Signature Guidelines</h3>
                            <div class="mt-2 text-sm text-blue-700">
                                <ul class="list-disc list-inside space-y-1">
                                    <li>Use a clear, high-quality image of your signature</li>
                                    <li>Recommended size: 200x80 pixels or similar aspect ratio</li>
                                    <li>Supported formats: PNG, JPG, GIF (max 2MB)</li>
                                    <li>Signature should be on a white or transparent background</li>
                                    <li>This signature will appear on all grade sheets and official documents</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex space-x-3">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                            <i class="fas fa-upload mr-2"></i>Upload Signature
                        </button>
                        
                        @if($user->signature)
                            <button type="button" onclick="confirmRemove()" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                <i class="fas fa-trash mr-2"></i>Remove Signature
                            </button>
                        @endif
                    </div>
                </div>
            </form>
        </div>

        <!-- Preview Section -->
        @if($user->signature)
            <div class="bg-white shadow rounded-lg p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Grade Sheet Preview</h3>
                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <div class="text-center">
                        <h4 class="text-lg font-semibold text-gray-900 mb-4">Sample Grade Sheet</h4>
                        <div class="inline-block border border-gray-300 rounded p-4 bg-white">
                            <div class="text-sm text-gray-600 mb-2">Authorized Signature:</div>
                            <img src="{{ Storage::url($user->signature) }}" alt="Signature Preview" class="h-12 w-24 object-contain mx-auto">
                            <div class="text-xs text-gray-500 mt-1">{{ $user->name }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
function confirmRemove() {
    if (confirm('Are you sure you want to remove your signature? This will affect all grade sheets and official documents.')) {
        // Create a form to submit the removal request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.settings.signature.remove") }}';
        
        // Add CSRF token
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        form.appendChild(csrfToken);
        
        // Add method override
        const methodField = document.createElement('input');
        methodField.type = 'hidden';
        methodField.name = '_method';
        methodField.value = 'DELETE';
        form.appendChild(methodField);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Preview uploaded image
document.getElementById('signature').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // You can add preview functionality here if needed
            console.log('File selected:', file.name);
        };
        reader.readAsDataURL(file);
    }
});
</script>
@endsection
