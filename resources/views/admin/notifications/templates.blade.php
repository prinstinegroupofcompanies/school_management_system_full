@extends('layouts.app')

@section('title', 'Notification Templates')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Notification Templates</h1>
        <a href="{{ route('admin.notifications.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            Back to Notifications
        </a>
    </div>

    <div class="max-w-6xl mx-auto">
        <!-- Template Categories -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($templates as $key => $template)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $template['title'] }}</h3>
                        <p class="text-sm text-gray-500">Template ID: {{ $key }}</p>
                    </div>
                    
                    <div class="px-6 py-4">
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Message Template:</h4>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ $template['message'] }}</p>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">Available Variables:</h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($template['variables'] as $variable)
                                    <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded-full">
                                        @{{ '{{' }} {{ $variable }} @{{ '}}' }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                        
                        <div class="flex justify-end">
                            <button onclick="useTemplate('{{ $key }}', '{{ addslashes($template['title']) }}', '{{ addslashes($template['message']) }}')" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                                Use Template
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Template Usage Instructions -->
        <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-blue-900 mb-4">How to Use Templates</h3>
            <div class="space-y-3 text-sm text-blue-800">
                <p>1. Click "Use Template" on any template above to automatically fill the notification form</p>
                <p>2. Replace the variables (like {student_name}, {amount}) with actual values</p>
                <p>3. Customize the message as needed for your specific notification</p>
                <p>4. Select your recipients and delivery method</p>
                <p>5. Send the notification</p>
            </div>
        </div>

        <!-- Variable Reference -->
        <div class="mt-8 bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Variable Reference</h3>
                <p class="text-sm text-gray-600">Common variables you can use in your notification templates</p>
            </div>
            <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="space-y-2">
                        <h4 class="font-medium text-gray-900">Student Variables</h4>
                        <div class="space-y-1 text-sm">
                            <div class="flex items-center space-x-2">
                                <code class="px-2 py-1 bg-gray-100 rounded text-xs">{student_name}</code>
                                <span class="text-gray-600">Student's full name</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <code class="px-2 py-1 bg-gray-100 rounded text-xs">{student_id}</code>
                                <span class="text-gray-600">Student ID number</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <code class="px-2 py-1 bg-gray-100 rounded text-xs">{class_name}</code>
                                <span class="text-gray-600">Student's class</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <h4 class="font-medium text-gray-900">Financial Variables</h4>
                        <div class="space-y-1 text-sm">
                            <div class="flex items-center space-x-2">
                                <code class="px-2 py-1 bg-gray-100 rounded text-xs">{amount}</code>
                                <span class="text-gray-600">Payment amount</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <code class="px-2 py-1 bg-gray-100 rounded text-xs">{due_date}</code>
                                <span class="text-gray-600">Due date</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <code class="px-2 py-1 bg-gray-100 rounded text-xs">{payment_method}</code>
                                <span class="text-gray-600">Payment method</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <h4 class="font-medium text-gray-900">Academic Variables</h4>
                        <div class="space-y-1 text-sm">
                            <div class="flex items-center space-x-2">
                                <code class="px-2 py-1 bg-gray-100 rounded text-xs">{subject_name}</code>
                                <span class="text-gray-600">Subject name</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <code class="px-2 py-1 bg-gray-100 rounded text-xs">{exam_title}</code>
                                <span class="text-gray-600">Exam title</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <code class="px-2 py-1 bg-gray-100 rounded text-xs">{exam_date}</code>
                                <span class="text-gray-600">Exam date</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <code class="px-2 py-1 bg-gray-100 rounded text-xs">{start_time}</code>
                                <span class="text-gray-600">Start time</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function useTemplate(templateKey, title, message) {
    // Store template data in session storage
    sessionStorage.setItem('notification_template', JSON.stringify({
        key: templateKey,
        title: title,
        message: message
    }));
    
    // Redirect to create notification page
    window.location.href = '{{ route("admin.notifications.create") }}';
}

// Check if we have template data from session storage
document.addEventListener('DOMContentLoaded', function() {
    const templateData = sessionStorage.getItem('notification_template');
    if (templateData) {
        const template = JSON.parse(templateData);
        
        // Fill the form with template data
        const titleField = document.getElementById('title');
        const messageField = document.getElementById('message');
        const typeField = document.getElementById('type');
        
        if (titleField) titleField.value = template.title;
        if (messageField) messageField.value = template.message;
        if (typeField) typeField.value = template.key;
        
        // Clear the session storage
        sessionStorage.removeItem('notification_template');
    }
});
</script>
@endsection
