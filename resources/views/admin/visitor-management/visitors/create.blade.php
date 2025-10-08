@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">
                        <i class="fas fa-plus mr-2"></i>
                        Create New Visitor
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.visitor-management.visitors.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="first_name">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" 
                                           id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" 
                                           id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email') }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">Phone</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_number">ID Number</label>
                                    <input type="text" class="form-control @error('id_number') is-invalid @enderror" 
                                           id="id_number" name="id_number" value="{{ old('id_number') }}">
                                    @error('id_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="id_type">ID Type</label>
                                    <select class="form-control @error('id_type') is-invalid @enderror" 
                                            id="id_type" name="id_type">
                                        <option value="">Select ID Type</option>
                                        <option value="national_id" {{ old('id_type') === 'national_id' ? 'selected' : '' }}>National ID</option>
                                        <option value="passport" {{ old('id_type') === 'passport' ? 'selected' : '' }}>Passport</option>
                                        <option value="drivers_license" {{ old('id_type') === 'drivers_license' ? 'selected' : '' }}>Driver's License</option>
                                        <option value="other" {{ old('id_type') === 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('id_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="organization">Organization</label>
                                    <input type="text" class="form-control @error('organization') is-invalid @enderror" 
                                           id="organization" name="organization" value="{{ old('organization') }}">
                                    @error('organization')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="position">Position</label>
                                    <input type="text" class="form-control @error('position') is-invalid @enderror" 
                                           id="position" name="position" value="{{ old('position') }}">
                                    @error('position')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="3">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="category_id">Category</label>
                            <select class="form-control @error('category_id') is-invalid @enderror" 
                                    id="category_id" name="category_id">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_blacklisted" name="is_blacklisted" value="1" {{ old('is_blacklisted') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_blacklisted">
                                    Blacklisted
                                </label>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="blacklist_reason">Blacklist Reason</label>
                            <textarea class="form-control @error('blacklist_reason') is-invalid @enderror" 
                                      id="blacklist_reason" name="blacklist_reason" rows="2" 
                                      {{ old('is_blacklisted') ? '' : 'disabled' }}>{{ old('blacklist_reason') }}</textarea>
                            @error('blacklist_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>
                                Create Visitor
                            </button>
                            <a href="{{ route('admin.visitor-management.visitors.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times mr-1"></i>
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('is_blacklisted').addEventListener('change', function() {
    const reasonField = document.getElementById('blacklist_reason');
    reasonField.disabled = !this.checked;
    if (!this.checked) {
        reasonField.value = '';
    }
});
</script>
@endsection
