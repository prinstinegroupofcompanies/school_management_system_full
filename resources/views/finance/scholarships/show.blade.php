@extends('layouts.app')

@section('title', 'Scholarship Details')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <div class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $scholarship->name }}</h1>
                    <nav class="flex mt-2" aria-label="Breadcrumb">
                        <ol class="flex items-center space-x-4">
                            <li>
                                <a href="{{ route('finance.dashboard') }}" class="text-gray-400 hover:text-gray-500">
                                    <span class="sr-only">Finance</span>
                                    Finance
                                </a>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <a href="{{ route('finance.scholarships.index') }}" class="ml-4 text-gray-400 hover:text-gray-500">Scholarships</a>
                                </div>
                            </li>
                            <li>
                                <div class="flex items-center">
                                    <svg class="flex-shrink-0 h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                                    </svg>
                                    <span class="ml-4 text-gray-500">Details</span>
                                </div>
                            </li>
                        </ol>
                    </nav>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('finance.scholarships.edit', $scholarship->id) }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <a href="{{ route('finance.scholarships.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Scholarship Information -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Details -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">Scholarship Details</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Scholarship Code</label>
                                <p class="mt-1 text-sm text-gray-900 font-mono">{{ $scholarship->code }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Type</label>
                                <span class="mt-1 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    @if($scholarship->type === 'merit') bg-blue-100 text-blue-800
                                    @elseif($scholarship->type === 'need') bg-green-100 text-green-800
                                    @elseif($scholarship->type === 'sports') bg-purple-100 text-purple-800
                                    @elseif($scholarship->type === 'community') bg-yellow-100 text-yellow-800
                                    @else bg-gray-100 text-gray-800
                                    @endif">
                                    {{ ucfirst($scholarship->type) }}
                                </span>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Amount</label>
                                <p class="mt-1 text-sm text-gray-900 font-semibold">${{ number_format($scholarship->amount, 2) }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Max Recipients</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $scholarship->max_recipients }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Current Recipients</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $scholarship->current_recipients }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Available Slots</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $scholarship->max_recipients - $scholarship->current_recipients }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Academic Year</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $scholarship->academic_year }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-500">Application Deadline</label>
                                <p class="mt-1 text-sm text-gray-900">
                                    {{ $scholarship->application_deadline ? $scholarship->application_deadline->format('M d, Y') : 'N/A' }}
                                </p>
                            </div>
                            
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-500">Description</label>
                                <p class="mt-1 text-sm text-gray-900">{{ $scholarship->description }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recipients List -->
                @if($applications->count() > 0)
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">Recipients ({{ $applications->count() }})</h3>
                        
                        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Student
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Class
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Application Date
                                        </th>
                                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($applications as $application)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <div class="h-10 w-10 rounded-full bg-blue-500 flex items-center justify-center">
                                                        <span class="text-sm font-medium text-white">
                                                            {{ substr($application->student->user->name ?? 'U', 0, 1) }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">{{ $application->student->user->name ?? 'Unknown' }}</div>
                                                    <div class="text-sm text-gray-500">{{ $application->student->admission_number }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $application->student->classRoom->name ?? 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ $application->application_date ? $application->application_date->format('M d, Y') : 'N/A' }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                @if($application->status === 'approved') bg-green-100 text-green-800
                                                @elseif($application->status === 'pending') bg-yellow-100 text-yellow-800
                                                @elseif($application->status === 'rejected') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800
                                                @endif">
                                                {{ ucfirst($application->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Statistics Sidebar -->
            <div class="space-y-6">
                <!-- Quick Stats -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-6">Statistics</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Total Applicants</span>
                                <span class="text-sm font-semibold text-gray-900">{{ $stats['total_applicants'] }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Approved</span>
                                <span class="text-sm font-semibold text-green-600">{{ $stats['approved_applicants'] }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Pending</span>
                                <span class="text-sm font-semibold text-yellow-600">{{ $stats['pending_applicants'] }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Rejected</span>
                                <span class="text-sm font-semibold text-red-600">{{ $stats['rejected_applicants'] }}</span>
                            </div>
                            
                            <div class="border-t pt-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-sm font-medium text-gray-500">Total Amount Awarded</span>
                                    <span class="text-sm font-semibold text-gray-900">${{ number_format($stats['total_amount_awarded'], 2) }}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-500">Available Slots</span>
                                <span class="text-sm font-semibold text-blue-600">{{ $stats['available_slots'] }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Recipients Progress</h3>
                        
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                            @php
                                $percentage = $scholarship->max_recipients > 0 ? ($scholarship->current_recipients / $scholarship->max_recipients) * 100 : 0;
                            @endphp
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" style="width: {{ $percentage }}%"></div>
                        </div>
                        
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>{{ $scholarship->current_recipients }} recipients</span>
                            <span>{{ number_format($percentage, 1) }}% filled</span>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="bg-white shadow rounded-lg">
                    <div class="px-4 py-5 sm:p-6">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Actions</h3>
                        
                        <div class="space-y-3">
                            <a href="{{ route('finance.scholarships.edit', $scholarship->id) }}" 
                               class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Scholarship
                            </a>
                            
                            <form action="{{ route('finance.scholarships.destroy', $scholarship->id) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this scholarship? This action cannot be undone.')"
                                  class="w-full">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete Scholarship
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection