@extends('layouts.app')

@section('title', 'Create E-Signature Template')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-2xl font-bold text-gray-900 mb-6">Create E-Signature Template</h1>
        <form action="{{ route('admin.e-signatures.templates.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Template Name -->
            <div>
                <label for="template_name" class="block text-sm font-medium text-gray-700">Template Name <span class="text-red-500">*</span></label>
                <input type="text" id="template_name" name="template_name" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Document Type -->
            <div>
                <label for="document_type" class="block text-sm font-medium text-gray-700">Document Type <span class="text-red-500">*</span></label>
                <select id="document_type" name="document_type" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select Document Type</option>
                    @foreach($documentTypes as $type)
                        <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea id="description" name="description" rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
            </div>

            <!-- Signature Fields -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Signature Fields <span class="text-red-500">*</span></label>
                <div id="signature-fields-list" class="space-y-2">
                    <div class="flex items-center space-x-2">
                        <input type="text" name="signature_fields[]" placeholder="Enter signature field name" required
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <button type="button" class="remove-field text-red-600 hover:text-red-800">Remove</button>
                    </div>
                </div>
                <button type="button" id="add-signature-field" class="mt-2 px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm font-medium">Add Signature Field</button>
            </div>

            <!-- Signature Requirements -->
            <div class="flex items-center">
                <input type="checkbox" id="requires_witness" name="requires_witness" value="1"
                       class="h-4 w-4 text-blue-600 border-gray-300 rounded">
                <label for="requires_witness" class="ml-2 block text-sm text-gray-700">Requires Witness</label>
            </div>

            <!-- Expiry Days -->
            <div>
                <label for="expiry_days" class="block text-sm font-medium text-gray-700">Expiry Days <span class="text-red-500">*</span></label>
                <input type="number" id="expiry_days" name="expiry_days" value="30" min="1" required
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
            </div>

            <!-- Buttons -->
            <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-200">
                <a href="{{ route('admin.e-signatures.templates') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    Create Template
                </button>
            </div>
        </form>
    </div>
</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const addBtn = document.getElementById('add-signature-field');
    const fieldsList = document.getElementById('signature-fields-list');
    addBtn.addEventListener('click', function() {
        const div = document.createElement('div');
        div.className = 'flex items-center space-x-2';
        div.innerHTML = `<input type="text" name="signature_fields[]" placeholder="Enter signature field name" required
            class="block w-full rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
            <button type="button" class="remove-field text-red-600 hover:text-red-800">Remove</button>`;
        fieldsList.appendChild(div);
    });
    fieldsList.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-field')) {
            e.target.parentElement.remove();
        }
    });
});
</script>
@endpush
@endsection