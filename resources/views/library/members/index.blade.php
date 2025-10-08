@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('library.index') }}">Library</a></li>
                        <li class="breadcrumb-item active">Members</li>
                    </ol>
                </div>
                <h4 class="page-title">Library Members</h4>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $memberStats['total_members'] }}</h4>
                            <p class="mb-0">Total Members</p>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-account-group mdi-36px"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $memberStats['active_members'] }}</h4>
                            <p class="mb-0">Active Members</p>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-account-check mdi-36px"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $memberStats['suspended_members'] }}</h4>
                            <p class="mb-0">Suspended</p>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-account-cancel mdi-36px"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="mb-0">{{ $memberStats['members_with_fines'] }}</h4>
                            <p class="mb-0">With Fines</p>
                        </div>
                        <div class="align-self-center">
                            <i class="mdi mdi-currency-usd mdi-36px"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Members Management</h5>
                    <a href="{{ route('library.members.create') }}" class="btn btn-primary">
                        <i class="mdi mdi-plus"></i> Add New Member
                    </a>
                </div>
                <div class="card-body">
                    <!-- Search and Filter -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('library.members') }}" class="d-flex">
                                <input type="text" name="search" class="form-control me-2" 
                                       placeholder="Search members..." value="{{ request('search') }}">
                                <button type="submit" class="btn btn-outline-primary">Search</button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('library.members') }}" class="d-flex">
                                <select name="status" class="form-control me-2">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expired</option>
                                </select>
                                <button type="submit" class="btn btn-outline-secondary">Filter</button>
                            </form>
                        </div>
                    </div>

                    <!-- Members Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Photo</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Member ID</th>
                                    <th>Status</th>
                                    <th>Fine Balance</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($members as $member)
                                <tr>
                                    <td>
                                        @if($member->user && $member->user->profile_photo)
                                            <img src="{{ asset('storage/' . $member->user->profile_photo) }}" 
                                                 alt="{{ $member->user->name }}" class="rounded-circle" 
                                                 style="width: 40px; height: 40px;">
                                        @else
                                            <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="mdi mdi-account mdi-20px text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div>
                                            <strong>{{ $member->user->name ?? 'N/A' }}</strong>
                                            @if($member->user && $member->user->student)
                                                <br><small class="text-muted">{{ $member->user->student->classRoom->name ?? 'N/A' }}</small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>{{ $member->user->email ?? 'N/A' }}</td>
                                    <td>{{ $member->phone ?? 'N/A' }}</td>
                                    <td>
                                        <span class="badge bg-info">{{ $member->member_id }}</span>
                                    </td>
                                    <td>
                                        @if($member->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-danger">Inactive</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($member->fine_balance > 0)
                                            <span class="text-danger font-weight-bold">${{ number_format($member->fine_balance, 2) }}</span>
                                        @else
                                            <span class="text-success">$0.00</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('library.members.show', $member) }}" class="btn btn-sm btn-outline-info">
                                                <i class="mdi mdi-eye"></i>
                                            </a>
                                            <a href="{{ route('library.members.edit', $member) }}" class="btn btn-sm btn-outline-warning">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <form method="POST" action="{{ route('library.members.destroy', $member) }}" 
                                                  class="d-inline" onsubmit="return confirm('Are you sure you want to delete this member?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                                    <i class="mdi mdi-delete"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="mdi mdi-account-group mdi-48px"></i>
                                            <p class="mt-2">No members found</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Showing {{ $members->firstItem() ?? 0 }} to {{ $members->lastItem() ?? 0 }} of {{ $members->total() }} entries
                        </div>
                        <div>
                            {{ $members->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection