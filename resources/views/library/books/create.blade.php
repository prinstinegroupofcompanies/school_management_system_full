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
                        <li class="breadcrumb-item"><a href="{{ route('library.books') }}">Books</a></li>
                        <li class="breadcrumb-item active">Add New Book</li>
                    </ol>
                </div>
                <h4 class="page-title">Add New Book</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Book Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('library.books.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Basic Information -->
                                <div class="form-group">
                                    <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                           id="title" name="title" value="{{ old('title') }}" required>
                                    @error('title')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="author" class="form-label">Author <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('author') is-invalid @enderror" 
                                                   id="author" name="author" value="{{ old('author') }}" required>
                                            @error('author')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="publisher" class="form-label">Publisher</label>
                                            <input type="text" class="form-control @error('publisher') is-invalid @enderror" 
                                                   id="publisher" name="publisher" value="{{ old('publisher') }}">
                                            @error('publisher')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="isbn" class="form-label">ISBN</label>
                                            <input type="text" class="form-control @error('isbn') is-invalid @enderror" 
                                                   id="isbn" name="isbn" value="{{ old('isbn') }}">
                                            @error('isbn')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="edition" class="form-label">Edition</label>
                                            <input type="text" class="form-control @error('edition') is-invalid @enderror" 
                                                   id="edition" name="edition" value="{{ old('edition') }}">
                                            @error('edition')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="publication_year" class="form-label">Publication Year</label>
                                            <input type="number" class="form-control @error('publication_year') is-invalid @enderror" 
                                                   id="publication_year" name="publication_year" value="{{ old('publication_year') }}" min="1900" max="{{ date('Y') }}">
                                            @error('publication_year')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="language" class="form-label">Language</label>
                                            <select class="form-control @error('language') is-invalid @enderror" id="language" name="language">
                                                <option value="">Select Language</option>
                                                <option value="English" {{ old('language') == 'English' ? 'selected' : '' }}>English</option>
                                                <option value="French" {{ old('language') == 'French' ? 'selected' : '' }}>French</option>
                                                <option value="Spanish" {{ old('language') == 'Spanish' ? 'selected' : '' }}>Spanish</option>
                                                <option value="Arabic" {{ old('language') == 'Arabic' ? 'selected' : '' }}>Arabic</option>
                                                <option value="Other" {{ old('language') == 'Other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('language')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Category and Type -->
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                            <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
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
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="subcategory_id" class="form-label">Subcategory</label>
                                            <select class="form-control @error('subcategory_id') is-invalid @enderror" id="subcategory_id" name="subcategory_id">
                                                <option value="">Select Subcategory</option>
                                            </select>
                                            @error('subcategory_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Physical/Digital -->
                                <div class="form-group">
                                    <label class="form-label">Book Type</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="is_digital" id="physical" value="0" {{ old('is_digital') == '0' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="physical">Physical Book</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="is_digital" id="digital" value="1" {{ old('is_digital') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="digital">Digital Book</label>
                                    </div>
                                </div>

                                <!-- Inventory -->
                                <div class="row" id="inventoryFields">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="total_copies" class="form-label">Total Copies <span class="text-danger">*</span></label>
                                            <input type="number" class="form-control @error('total_copies') is-invalid @enderror" 
                                                   id="total_copies" name="total_copies" value="{{ old('total_copies', 1) }}" min="1" required>
                                            @error('total_copies')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="available_copies" class="form-label">Available Copies</label>
                                            <input type="number" class="form-control @error('available_copies') is-invalid @enderror" 
                                                   id="available_copies" name="available_copies" value="{{ old('available_copies', 1) }}" min="0">
                                            @error('available_copies')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="price" class="form-label">Price (LRD)</label>
                                            <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                                   id="price" name="price" value="{{ old('price') }}" step="0.01" min="0">
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- File Upload for Digital Books -->
                                <div class="form-group" id="fileUploadGroup" style="display: none;">
                                    <label for="file_path" class="form-label">Book File</label>
                                    <input type="file" class="form-control @error('file_path') is-invalid @enderror" 
                                           id="file_path" name="file_path" accept=".pdf,.epub,.mobi,.doc,.docx">
                                    <small class="form-text text-muted">Supported formats: PDF, EPUB, MOBI, DOC, DOCX</small>
                                    @error('file_path')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Description -->
                                <div class="form-group">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" name="description" rows="3">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Tags -->
                                <div class="form-group">
                                    <label for="tags" class="form-label">Tags</label>
                                    <input type="text" class="form-control @error('tags') is-invalid @enderror" 
                                           id="tags" name="tags" value="{{ old('tags') }}" placeholder="Enter tags separated by commas">
                                    <small class="form-text text-muted">Separate tags with commas (e.g., fiction, mystery, adventure)</small>
                                    @error('tags')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Cover Image -->
                                <div class="form-group">
                                    <label for="cover_image" class="form-label">Cover Image</label>
                                    <input type="file" class="form-control @error('cover_image') is-invalid @enderror" 
                                           id="cover_image" name="cover_image" accept="image/*">
                                    <small class="form-text text-muted">Recommended size: 300x400px</small>
                                    @error('cover_image')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Preview -->
                                <div class="form-group">
                                    <label class="form-label">Preview</label>
                                    <div id="imagePreview" class="border rounded p-2 text-center" style="min-height: 200px;">
                                        <i class="mdi mdi-image text-muted" style="font-size: 48px;"></i>
                                        <p class="text-muted mt-2">No image selected</p>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="form-group">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                                        <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                                        <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group text-right">
                            <a href="{{ route('library.books') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">Add Book</button>
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
// Show/hide file upload based on book type
document.querySelectorAll('input[name="is_digital"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const fileUploadGroup = document.getElementById('fileUploadGroup');
        const inventoryFields = document.getElementById('inventoryFields');
        
        if (this.value === '1') {
            fileUploadGroup.style.display = 'block';
            inventoryFields.style.display = 'none';
        } else {
            fileUploadGroup.style.display = 'none';
            inventoryFields.style.display = 'block';
        }
    });
});

// Image preview
document.getElementById('cover_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('imagePreview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `<img src="${e.target.result}" class="img-fluid" style="max-height: 200px;">`;
        };
        reader.readAsDataURL(file);
    } else {
        preview.innerHTML = '<i class="mdi mdi-image text-muted" style="font-size: 48px;"></i><p class="text-muted mt-2">No image selected</p>';
    }
});

// Load subcategories when category changes
document.getElementById('category_id').addEventListener('change', function() {
    const categoryId = this.value;
    const subcategorySelect = document.getElementById('subcategory_id');
    
    subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
    
    if (categoryId) {
        fetch(`/api/categories/${categoryId}/subcategories`)
            .then(response => response.json())
            .then(data => {
                data.forEach(subcategory => {
                    const option = document.createElement('option');
                    option.value = subcategory.id;
                    option.textContent = subcategory.name;
                    subcategorySelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error loading subcategories:', error));
    }
});

// Auto-calculate available copies
document.getElementById('total_copies').addEventListener('input', function() {
    const totalCopies = parseInt(this.value) || 0;
    const availableCopies = document.getElementById('available_copies');
    availableCopies.value = totalCopies;
});
</script>
@endpush
