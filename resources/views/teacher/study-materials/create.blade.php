@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Upload Study Material</h1>
        <a href="{{ route('teacher.study-materials.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            Back to Materials
        </a>
    </div>

    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('teacher.study-materials.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700">Title</label>
                <input type="text" name="title" class="mt-1 block w-full border-gray-300 rounded-md" required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Subject</label>
                    <select name="subject_id" class="mt-1 block w-full border-gray-300 rounded-md" required>
                        <option value="">Select subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Class</label>
                    <select name="class_id" class="mt-1 block w-full border-gray-300 rounded-md" required>
                        <option value="">Select class</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Material Type</label>
                <select name="material_type" id="material_type" class="mt-1 block w-full border-gray-300 rounded-md" onchange="toggleFields()" required>
                    <option value="">Select type</option>
                    <option value="document">Document (PDF, DOC, etc.)</option>
                    <option value="video">Video</option>
                    <option value="image">Image</option>
                    <option value="link">External Link</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md" placeholder="Describe the material and how students should use it..."></textarea>
            </div>

            <div id="file_upload" class="hidden">
                <label class="block text-sm font-medium text-gray-700">Upload File</label>
                <input type="file" name="file" class="mt-1 block w-full border-gray-300 rounded-md" accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.mp4,.avi,.mov">
                <p class="mt-1 text-sm text-gray-500">Maximum file size: 10MB</p>
            </div>

            <div id="link_input" class="hidden">
                <label class="block text-sm font-medium text-gray-700">External Link URL</label>
                <input type="url" name="link_url" class="mt-1 block w-full border-gray-300 rounded-md" placeholder="https://example.com">
                <p class="mt-1 text-sm text-gray-500">Provide a link to external resources (YouTube, Google Drive, etc.)</p>
            </div>

            <div class="flex justify-end space-x-4">
                <button type="button" onclick="history.back()" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Upload Material
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleFields() {
    const materialType = document.getElementById('material_type').value;
    const fileUpload = document.getElementById('file_upload');
    const linkInput = document.getElementById('link_input');
    
    // Hide both initially
    fileUpload.classList.add('hidden');
    linkInput.classList.add('hidden');
    
    // Show appropriate field based on type
    if (materialType === 'link') {
        linkInput.classList.remove('hidden');
    } else if (materialType && materialType !== 'link') {
        fileUpload.classList.remove('hidden');
    }
}
</script>
@endsection
