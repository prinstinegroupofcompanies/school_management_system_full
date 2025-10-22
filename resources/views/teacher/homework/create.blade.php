@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Create Homework Assignment</h1>
        <a href="{{ route('teacher.homework.index') }}" class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700">
            Back to Homework
        </a>
    </div>

    <div class="max-w-4xl mx-auto bg-white rounded-lg shadow p-6">
        <form method="POST" action="{{ route('teacher.homework.store') }}" class="space-y-6">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Title</label>
                    <input type="text" name="title" class="mt-1 block w-full border-gray-300 rounded-md" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Assignment Type</label>
                    <select name="assignment_type" class="mt-1 block w-full border-gray-300 rounded-md" required>
                        <option value="">Select type</option>
                        @foreach($assignmentTypes as $key => $type)
                            <option value="{{ $key }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
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

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Due Date</label>
                    <input type="datetime-local" name="due_date" class="mt-1 block w-full border-gray-300 rounded-md" required>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700">Total Points</label>
                    <input type="number" name="total_points" class="mt-1 block w-full border-gray-300 rounded-md" value="100" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md" placeholder="Assignment instructions and requirements..." required></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Materials Needed</label>
                <textarea name="materials" rows="3" class="mt-1 block w-full border-gray-300 rounded-md" placeholder="List any materials or resources needed..."></textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Submission Instructions</label>
                <textarea name="submission_instructions" rows="3" class="mt-1 block w-full border-gray-300 rounded-md" placeholder="How should students submit their work?"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center">
                    <input type="checkbox" name="allow_late_submission" id="allow_late_submission" class="h-4 w-4 text-blue-600">
                    <label for="allow_late_submission" class="ml-2 text-sm text-gray-700">Allow late submissions</label>
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" name="is_published" id="is_published" class="h-4 w-4 text-blue-600">
                    <label for="is_published" class="ml-2 text-sm text-gray-700">Publish immediately</label>
                </div>
            </div>

            <div class="flex justify-end space-x-4">
                <button type="button" onclick="history.back()" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                    Cancel
                </button>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700">
                    Create Assignment
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-save draft functionality
    const form = document.querySelector('form');
    const inputs = form.querySelectorAll('input, textarea, select');
    
    // Auto-save every 30 seconds
    setInterval(function() {
        autoSaveDraft();
    }, 30000);

    // Save draft on input change
    inputs.forEach(input => {
        input.addEventListener('input', debounce(autoSaveDraft, 2000));
    });

    function autoSaveDraft() {
        const formData = new FormData(form);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));
        formData.append('auto_save', 'true');

        fetch('{{ route("teacher.homework.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Draft auto-saved', 'success');
            }
        })
        .catch(error => {
            console.log('Auto-save failed:', error);
        });
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' : 
            type === 'error' ? 'bg-red-500 text-white' : 
            'bg-blue-500 text-white'
        }`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }

    // Real-time character count for textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        const maxLength = textarea.getAttribute('maxlength');
        if (maxLength) {
            const counter = document.createElement('div');
            counter.className = 'text-sm text-gray-500 mt-1 text-right';
            counter.textContent = `0/${maxLength} characters`;
            
            textarea.parentNode.appendChild(counter);
            
            textarea.addEventListener('input', function() {
                const currentLength = this.value.length;
                counter.textContent = `${currentLength}/${maxLength} characters`;
                
                if (currentLength > maxLength * 0.9) {
                    counter.className = 'text-sm text-red-500 mt-1 text-right';
                } else if (currentLength > maxLength * 0.8) {
                    counter.className = 'text-sm text-yellow-500 mt-1 text-right';
                } else {
                    counter.className = 'text-sm text-gray-500 mt-1 text-right';
                }
            });
        }
    });

    // Form validation with real-time feedback
    const requiredFields = form.querySelectorAll('[required]');
    requiredFields.forEach(field => {
        field.addEventListener('blur', function() {
            validateField(this);
        });
    });

    function validateField(field) {
        const value = field.value.trim();
        const isValid = value !== '';
        
        if (isValid) {
            field.classList.remove('border-red-300');
            field.classList.add('border-green-300');
        } else {
            field.classList.remove('border-green-300');
            field.classList.add('border-red-300');
        }
    }

    // Due date validation
    const dueDateInput = document.querySelector('input[name="due_date"]');
    if (dueDateInput) {
        dueDateInput.addEventListener('change', function() {
            const dueDate = new Date(this.value);
            const now = new Date();
            
            if (dueDate <= now) {
                this.classList.add('border-red-300');
                showNotification('Due date must be in the future', 'error');
            } else {
                this.classList.remove('border-red-300');
                this.classList.add('border-green-300');
            }
        });
    }
});
</script>
@endpush
