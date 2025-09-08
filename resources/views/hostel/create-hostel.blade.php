@extends('layouts.app')

@section('title', 'Add New Hostel')

@push('styles')
<style>
    .hostel-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 2rem 0;
        position: relative;
        overflow: hidden;
    }

    .hostel-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: 
            radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
        animation: float 20s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(5deg); }
    }
    
    .hostel-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        position: relative;
        z-index: 1;
    }

    .hostel-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3rem 2rem;
        border: none;
        position: relative;
        overflow: hidden;
    }

    .hostel-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
        animation: shine 3s ease-in-out infinite;
    }

    @keyframes shine {
        0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
        50% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        100% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
    }

    .hostel-header h3 {
        margin: 0;
        font-weight: 700;
        font-size: 2.2rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.3);
        position: relative;
        z-index: 2;
        background: linear-gradient(135deg, #ffffff 0%, #f0f0f0 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .form-section {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        position: relative;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .form-section:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    }

    .form-section h4 {
        color: #2d3748;
        font-weight: 700;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 3px solid transparent;
        position: relative;
        font-size: 1.4rem;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .form-section h4::after {
        content: '';
        position: absolute;
        bottom: -3px;
        left: 0;
        width: 60px;
        height: 3px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 2px;
        animation: expand 0.8s ease-out;
    }

    @keyframes expand {
        from { width: 0; }
        to { width: 60px; }
    }

    .form-group {
        margin-bottom: 2rem;
        position: relative;
    }

    .form-group label {
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.8rem;
        display: block;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
    }

    .form-group label::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: -5px;
        width: 30px;
        height: 2px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 1px;
        opacity: 0;
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .form-group:focus-within label::after {
        opacity: 1;
        width: 100px;
    }

    .form-control {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem 1.2rem;
        font-size: 1rem;
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        background: rgba(255, 255, 255, 0.9);
        position: relative;
        backdrop-filter: blur(5px);
    }

    .form-control:focus {
        border-color: transparent;
        background: rgba(255, 255, 255, 1);
        box-shadow: 
            0 0 0 3px rgba(102, 126, 234, 0.1),
            0 10px 25px rgba(102, 126, 234, 0.15),
            inset 0 1px 0 rgba(255, 255, 255, 0.6);
        transform: translateY(-2px);
    }

    .form-control.is-invalid {
        border-color: transparent;
        background: rgba(254, 245, 245, 0.9);
        box-shadow: 
            0 0 0 3px rgba(239, 68, 68, 0.1),
            0 10px 25px rgba(239, 68, 68, 0.15);
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 12px;
        padding: 1rem 2.5rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
        font-size: 1rem;
    }

    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.6s ease;
    }

    .btn-primary:hover::before {
        left: 100%;
    }

    .btn-primary:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        border: none;
        border-radius: 12px;
        padding: 1rem 2.5rem;
        font-weight: 700;
        transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        position: relative;
        overflow: hidden;
    }

    .btn-secondary:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
    }

    .invalid-feedback {
        color: #e53e3e;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        font-weight: 600;
        background: rgba(254, 245, 245, 0.8);
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        border-left: 4px solid #e53e3e;
        animation: shake 0.5s ease-in-out;
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }

    .text-danger {
        color: #e53e3e !important;
    }

    @media (max-width: 768px) {
        .hostel-container {
            padding: 1rem 0;
        }
        
        .form-section {
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .hostel-header {
            padding: 2rem 1.5rem;
        }
        
        .hostel-header h3 {
            font-size: 1.8rem;
        }
    }
</style>
@endpush

@section('content')
<div class="hostel-container">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <div class="card hostel-card">
                    <div class="card-header hostel-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3><i class="fas fa-building me-2"></i>Add New Hostel</h3>
                            <a href="{{ route('hostel.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back to Hostels
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <form action="{{ route('hostel.store') }}" method="POST">
                            @csrf
                            
                            <!-- Hostel Information -->
                            <div class="form-section">
                                <h4><i class="fas fa-building me-2"></i>Hostel Information</h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">Hostel Name <span class="text-danger">*</span></label>
                                            <input type="text" 
                                                   class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" 
                                                   name="name" 
                                                   value="{{ old('name') }}" 
                                                   placeholder="Enter hostel name"
                                                   required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="phone">Phone Number</label>
                                            <input type="tel" 
                                                   class="form-control @error('phone') is-invalid @enderror" 
                                                   id="phone" 
                                                   name="phone" 
                                                   value="{{ old('phone') }}" 
                                                   placeholder="Enter phone number">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="email">Email Address</label>
                                            <input type="email" 
                                                   class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" 
                                                   name="email" 
                                                   value="{{ old('email') }}" 
                                                   placeholder="Enter email address">
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="address">Address</label>
                                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                                      id="address" 
                                                      name="address" 
                                                      rows="4" 
                                                      placeholder="Enter complete hostel address">{{ old('address') }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="warden_name">Warden Name</label>
                                            <input type="text" 
                                                   class="form-control @error('warden_name') is-invalid @enderror" 
                                                   id="warden_name" 
                                                   name="warden_name" 
                                                   value="{{ old('warden_name') }}" 
                                                   placeholder="Enter warden name">
                                            @error('warden_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="warden_phone">Warden Phone</label>
                                            <input type="tel" 
                                                   class="form-control @error('warden_phone') is-invalid @enderror" 
                                                   id="warden_phone" 
                                                   name="warden_phone" 
                                                   value="{{ old('warden_phone') }}" 
                                                   placeholder="Enter warden phone">
                                            @error('warden_phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Capacity & Status -->
                            <div class="form-section">
                                <h4><i class="fas fa-users me-2"></i>Capacity & Status</h4>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="capacity">Total Capacity <span class="text-danger">*</span></label>
                                            <input type="number" 
                                                   class="form-control @error('capacity') is-invalid @enderror" 
                                                   id="capacity" 
                                                   name="capacity" 
                                                   value="{{ old('capacity', 100) }}" 
                                                   placeholder="Enter total capacity"
                                                   min="1"
                                                   required>
                                            @error('capacity')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select class="form-control @error('status') is-invalid @enderror" 
                                                    id="status" 
                                                    name="status">
                                                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                <option value="maintenance" {{ old('status') == 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="description">Description</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" 
                                                      name="description" 
                                                      rows="6" 
                                                      placeholder="Enter hostel description">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-section">
                                <div class="d-flex justify-content-center gap-3">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i> Add Hostel
                                    </button>
                                    <a href="{{ route('hostel.index') }}" class="btn btn-secondary btn-lg">
                                        <i class="fas fa-times me-2"></i> Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
