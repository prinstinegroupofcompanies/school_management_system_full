@extends('layouts.app')

@section('title', 'User Management')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">User Management</li>
                    </ol>
                </div>
                <h4 class="page-title">User Management</h4>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-account-multiple widget-icon"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" title="Total Users">Total Users</h5>
                    <h3 class="mt-3 mb-3">{{ $stats['total_users'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-account-check widget-icon bg-success-lighten text-success"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" title="Active Users">Active Users</h5>
                    <h3 class="mt-3 mb-3">{{ $stats['active_users'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-school widget-icon bg-info-lighten text-info"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" title="Students">Students</h5>
                    <h3 class="mt-3 mb-3">{{ $stats['students'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card widget-flat">
                <div class="card-body">
                    <div class="float-end">
                        <i class="mdi mdi-teach widget-icon bg-warning-lighten text-warning"></i>
                    </div>
                    <h5 class="text-muted fw-normal mt-0" title="Teachers">Teachers</h5>
                    <h3 class="mt-3 mb-3">{{ $stats['teachers'] ?? 0 }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- User Management -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-sm-4">
                            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                                <i class="mdi mdi-plus-circle me-1"></i> Add New User
                            </a>
                        </div>
                        <div class="col-sm-8">
                            <div class="text-sm-end">
                                <form method="GET" class="d-inline-flex">
                                    <input type="text" name="search" class="form-control me-2" placeholder="Search users..." value="{{ request('search') }}">
                                    <select name="user_type" class="form-select me-2">
                                        <option value="">All Types</option>
                                        <option value="admin" {{ request('user_type') == 'admin' ? 'selected' : '' }}>Admin</option>
                                        <option value="student" {{ request('user_type') == 'student' ? 'selected' : '' }}>Student</option>
                                        <option value="teacher" {{ request('user_type') == 'teacher' ? 'selected' : '' }}>Teacher</option>
                                        <option value="staff" {{ request('user_type') == 'staff' ? 'selected' : '' }}>Staff</option>
                                        <option value="finance" {{ request('user_type') == 'finance' ? 'selected' : '' }}>Finance</option>
                                    </select>
                                    <select name="status" class="form-select me-2">
                                        <option value="">All Status</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <button type="submit" class="btn btn-secondary">Filter</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-centered table-nowrap mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Created</th>
                                    <th style="width: 125px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary-lighten rounded me-3">
                                                <span class="avatar-title bg-primary text-primary rounded-circle font-weight-medium">
                                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                                </span>
                                            </div>
                                            <div>
                                                <h5 class="m-0 fw-normal">{{ $user->name }}</h5>
                                                <p class="mb-0 text-muted"><small>ID: {{ $user->id }}</small></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->user_type == 'admin' ? 'danger' : ($user->user_type == 'teacher' ? 'warning' : ($user->user_type == 'student' ? 'info' : 'secondary')) }}">
                                            {{ ucfirst($user->user_type) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at->format('M d, Y') }}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            @if($user->user_type !== 'admin' || User::where('user_type', 'admin')->count() > 1)
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="mdi mdi-account-off-outline" style="font-size: 48px;"></i>
                                            <p class="mt-2">No users found</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($users, 'hasPages') && $users->hasPages())
                    <div class="row mt-3">
                        <div class="col-sm-12 col-md-5">
                            <div class="dataTables_info">
                                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() ?? 0 }} entries
                            </div>
                        </div>
                        <div class="col-sm-12 col-md-7">
                            <div class="dataTables_paginate paging_simple_numbers">
                                {{ $users->links() }}
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
