@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900">All Lesson Plans</h1>
        <a href="{{ route('admin.lesson-plans.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors">
            Create Lesson Plan
        </a>
    </div>
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <form method="GET" action="{{ route('admin.lesson-plans.index') }}" class="grid grid-cols-1 md:grid-cols-6 gap-4">
            <select name="status" class="rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Status</option>
                @foreach($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>{{ ucfirst($status) }}</option>
                @endforeach
            </select>
            <select name="teacher_id" class="rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Teachers</option>
                @foreach($teachers as $teacher)
                    <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>
                        {{ $teacher->user->name ?? 'N/A' }}
                    </option>
                @endforeach
            </select>
            <select name="subject_id" class="rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Subjects</option>
                @foreach($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ request('subject_id') == $subject->id ? 'selected' : '' }}>
                        {{ $subject->name }}
                    </option>
                @endforeach
            </select>
            <select name="class_id" class="rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Classes</option>
                @foreach($classes as $class)
                    <option value="{{ $class->id }}" {{ request('class_id') == $class->id ? 'selected' : '' }}>
                        {{ $class->name }}
                    </option>
                @endforeach
            </select>
            <input type="text" name="title" placeholder="Title" value="{{ request('title') }}" class="rounded-md border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500" />
            <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors">
                Filter
            </button>
        </form>
    </div>
    <div class="bg-white rounded-lg shadow-lg p-6">
        <table class="min-w-full bg-white">
            <thead>
                <tr>
                    <th class="py-2 px-4 border-b">Title</th>
                    <th class="py-2 px-4 border-b">Teacher</th>
                    <th class="py-2 px-4 border-b">Subject</th>
                    <th class="py-2 px-4 border-b">Class</th>
                    <th class="py-2 px-4 border-b">Lesson Date</th>
                    <th class="py-2 px-4 border-b">Status</th>
                    <th class="py-2 px-4 border-b">Submitted</th>
                    <th class="py-2 px-4 border-b">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($lessonPlans as $plan)
                <tr>
                    <td class="py-2 px-4 border-b">{{ $plan->title }}</td>
                    <td class="py-2 px-4 border-b">{{ $plan->teacher->user->name ?? 'N/A' }}</td>
                    <td class="py-2 px-4 border-b">{{ $plan->subject->name ?? '' }}</td>
                    <td class="py-2 px-4 border-b">{{ $plan->class->name ?? 'N/A' }}</td>
                    <td class="py-2 px-4 border-b">{{ $plan->lesson_date }}</td>
                    <td class="py-2 px-4 border-b">{{ ucfirst($plan->status) }}</td>
                    <td class="py-2 px-4 border-b">{{ $plan->submitted_at ? $plan->submitted_at->format('Y-m-d') : '-' }}</td>
                    <td class="py-2 px-4 border-b">
                        <a href="{{ route('admin.lesson-plans.show', $plan->id) }}" class="text-blue-600 hover:underline">View</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-4 text-center text-gray-500">No lesson plans found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-4">
            {{ $lessonPlans->links() }}
        </div>
    </div>
</div>
@endsection