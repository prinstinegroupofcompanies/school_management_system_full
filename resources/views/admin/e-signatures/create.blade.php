@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <h1 class="text-3xl font-bold text-gray-900">Create E-Signature Request</h1>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('admin.e-signatures.signatures') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Signatures
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg">
            <form action="{{ route('admin.e-signatures.store') }}" method="POST" class="p-6 space-y-6">
                @csrf

                <!-- User Selection -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700">User *</label>
                        <select id="user_id" name="user_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('user_id') border-red-300 @enderror" required>
                            <option value="">Select a user</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="template_id" class="block text-sm font-medium text-gray-700">Template</label>
                        <select id="template_id" name="template_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('template_id') border-red-300 @enderror">
                            <option value="">No template</option>
                            @foreach($templates as $template)
                                <option value="{{ $template->id }}" {{ old('template_id') == $template->id ? 'selected' : '' }}>
                                    {{ $template->template_name }}
                                </option>
                            @endforeach
                        </select>
                        @error('template_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Document Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="document_type" class="block text-sm font-medium text-gray-700">Document Type *</label>
                        <select id="document_type" name="document_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('document_type') border-red-300 @enderror" required>
                            <option value="">Select document type</option>
                            @foreach($documentTypes as $key => $label)
                                <option value="{{ $key }}" {{ old('document_type') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('document_type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="signature_type" class="block text-sm font-medium text-gray-700">Signature Type *</label>
                        <select id="signature_type" name="signature_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('signature_type') border-red-300 @enderror" required>
                            <option value="">Select signature type</option>
                            @foreach($signatureTypes as $key => $label)
                                <option value="{{ $key }}" {{ old('signature_type') == $key ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('signature_type')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Document Title -->
                <div>
                    <label for="document_title" class="block text-sm font-medium text-gray-700">Document Title *</label>
                    <input type="text" id="document_title" name="document_title" value="{{ old('document_title') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('document_title') border-red-300 @enderror" 
                           placeholder="Enter document title" required>
                    @error('document_title')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Document Content -->
                <div>
                    <label for="document_content" class="block text-sm font-medium text-gray-700">Document Content</label>
                    <textarea id="document_content" name="document_content" rows="6" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('document_content') border-red-300 @enderror" 
                              placeholder="Enter document content">{{ old('document_content') }}</textarea>
                    @error('document_content')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Expiry Date -->
                <div>
                    <label for="expiry_date" class="block text-sm font-medium text-gray-700">Expiry Date *</label>
                    <input type="date" id="expiry_date" name="expiry_date" value="{{ old('expiry_date') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('expiry_date') border-red-300 @enderror" 
                           min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                    @error('expiry_date')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Witness Information -->
                <div class="border-t pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Witness Information</h3>
                    
                    <div class="flex items-center mb-4">
                        <input type="checkbox" id="requires_witness" name="requires_witness" value="1" 
                               {{ old('requires_witness') ? 'checked' : '' }}
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="requires_witness" class="ml-2 block text-sm text-gray-900">
                            Requires witness
                        </label>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="witness-fields" style="display: none;">
                        <div>
                            <label for="witness_name" class="block text-sm font-medium text-gray-700">Witness Name</label>
                            <input type="text" id="witness_name" name="witness_name" value="{{ old('witness_name') }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('witness_name') border-red-300 @enderror" 
                                   placeholder="Enter witness name">
                            @error('witness_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="witness_email" class="block text-sm font-medium text-gray-700">Witness Email</label>
                            <input type="email" id="witness_email" name="witness_email" value="{{ old('witness_email') }}" 
                                   class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('witness_email') border-red-300 @enderror" 
                                   placeholder="Enter witness email">
                            @error('witness_email')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Notification Emails -->
                <div>
                    <label for="notification_emails" class="block text-sm font-medium text-gray-700">Notification Emails</label>
                    <input type="text" id="notification_emails" name="notification_emails[]" value="{{ old('notification_emails.0') }}" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('notification_emails') border-red-300 @enderror" 
                           placeholder="Enter email addresses (comma-separated)">
                    @error('notification_emails')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
                    <textarea id="notes" name="notes" rows="3" 
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 @error('notes') border-red-300 @enderror" 
                              placeholder="Enter any additional notes">{{ old('notes') }}</textarea>
                    @error('notes')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="flex items-center justify-end space-x-4 pt-6 border-t">
                    <a href="{{ route('admin.e-signatures.signatures') }}" 
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm font-medium">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        <i class="fas fa-save mr-2"></i>
                        Create Signature Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const witnessCheckbox = document.getElementById('requires_witness');
    const witnessFields = document.getElementById('witness-fields');
    
    witnessCheckbox.addEventListener('change', function() {
        if (this.checked) {
            witnessFields.style.display = 'block';
        } else {
            witnessFields.style.display = 'none';
        }
    });
    
    // Initialize visibility based on old input
    if (witnessCheckbox.checked) {
        witnessFields.style.display = 'block';
    }
});
</script>
@endsection
