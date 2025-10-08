@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">
                        <i class="fas fa-check-circle mr-2"></i>
                        E-Signature Approvals
                    </h3>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-3">
                            <select class="form-control" id="approverFilter">
                                <option value="">All Approvers</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="levelFilter">
                                <option value="">All Levels</option>
                                @foreach($approvalLevels as $level)
                                    <option value="{{ $level }}">Level {{ $level }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select class="form-control" id="statusFilter">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="approved">Approved</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary" onclick="filterApprovals()">
                                <i class="fas fa-filter mr-1"></i>
                                Filter
                            </button>
                        </div>
                    </div>

                    <!-- Approvals Table -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Signature ID</th>
                                    <th>Document Type</th>
                                    <th>Approver</th>
                                    <th>Level</th>
                                    <th>Status</th>
                                    <th>Submitted</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($approvals as $approval)
                                <tr>
                                    <td>
                                        <strong>{{ $approval->signature->signature_id }}</strong>
                                    </td>
                                    <td>{{ ucfirst(str_replace('_', ' ', $approval->signature->document_type)) }}</td>
                                    <td>
                                        <strong>{{ $approval->approver->name }}</strong><br>
                                        <small class="text-muted">{{ $approval->approver->email }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-info">Level {{ $approval->approval_level }}</span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $approval->status === 'approved' ? 'success' : ($approval->status === 'rejected' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($approval->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $approval->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.e-signatures.approvals.show', $approval) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($approval->status === 'pending')
                                            <button type="button" class="btn btn-sm btn-success" onclick="approveSignature({{ $approval->id }})">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="rejectSignature({{ $approval->id }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        No approvals found
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $approvals->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function filterApprovals() {
    const approver = document.getElementById('approverFilter').value;
    const level = document.getElementById('levelFilter').value;
    const status = document.getElementById('statusFilter').value;
    
    let url = new URL(window.location);
    url.searchParams.set('approver_id', approver);
    url.searchParams.set('approval_level', level);
    url.searchParams.set('status', status);
    
    window.location.href = url.toString();
}

function approveSignature(approvalId) {
    if (confirm('Are you sure you want to approve this signature?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/e-signatures/approvals/${approvalId}/approve`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}

function rejectSignature(approvalId) {
    const reason = prompt('Please enter the reason for rejection:');
    if (reason) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/e-signatures/approvals/${approvalId}/reject`;
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        const reasonField = document.createElement('input');
        reasonField.type = 'hidden';
        reasonField.name = 'rejection_reason';
        reasonField.value = reason;
        
        form.appendChild(csrfToken);
        form.appendChild(reasonField);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endpush
