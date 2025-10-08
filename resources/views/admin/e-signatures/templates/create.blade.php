@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="card-title">
                        <i class="fas fa-plus mr-2"></i>
                        Create E-Signature Template
                    </h3>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.e-signatures.templates.store') }}" method="POST">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="template_name">Template Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('template_name') is-invalid @enderror" 
                                           id="template_name" name="template_name" value="{{ old('template_name') }}" required>
                                    @error('template_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="document_type">Document Type <span class="text-danger">*</span></label>
                                    <select class="form-control @error('document_type') is-invalid @enderror" 
                                            id="document_type" name="document_type" required>
                                        <option value="">Select Document Type</option>
                                        @foreach($documentTypes as $key => $label)
                                            <option value="{{ $key }}" {{ old('document_type') === $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('document_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="signature_fields">Signature Fields <span class="text-danger">*</span></label>
                            <div id="signature-fields-container">
                                @if(old('signature_fields'))
                                    @foreach(old('signature_fields') as $index => $field)
                                        <div class="input-group mb-2" data-field-index="{{ $index }}">
                                            <input type="text" class="form-control" name="signature_fields[]" value="{{ $field }}" required>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-danger remove-field">Remove</button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="input-group mb-2" data-field-index="0">
                                        <input type="text" class="form-control" name="signature_fields[]" placeholder="Enter signature field name" required>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-danger remove-field">Remove</button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <button type="button" class="btn btn-sm btn-secondary" id="add-signature-field">
                                <i class="fas fa-plus mr-1"></i>
                                Add Signature Field
                            </button>
                            @error('signature_fields')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="signature_requirements">Signature Requirements</label>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="requires_witness" name="requires_witness" value="1" {{ old('requires_witness') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="requires_witness">
                                            Requires Witness
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="expiry_days">Expiry Days <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control @error('expiry_days') is-invalid @enderror" 
                                               id="expiry_days" name="expiry_days" value="{{ old('expiry_days', 30) }}" min="1" max="365" required>
                                        @error('expiry_days')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save mr-1"></i>
                                Create Template
                            </button>
                            <a href="{{ route('admin.e-signatures.templates') }}" class="btn btn-secondary">
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
@endsection

@push('scripts')
<script>
let fieldIndex = {{ old('signature_fields') ? count(old('signature_fields')) : 1 }};

document.getElementById('add-signature-field').addEventListener('click', function() {
    const container = document.getElementById('signature-fields-container');
    const fieldHtml = `
        <div class="input-group mb-2" data-field-index="${fieldIndex}">
            <input type="text" class="form-control" name="signature_fields[]" placeholder="Enter signature field name" required>
            <div class="input-group-append">
                <button type="button" class="btn btn-outline-danger remove-field">Remove</button>
            </div>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', fieldHtml);
    fieldIndex++;
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-field')) {
        const fieldGroup = e.target.closest('.input-group');
        fieldGroup.remove();
    }
});
</script>
@endpush