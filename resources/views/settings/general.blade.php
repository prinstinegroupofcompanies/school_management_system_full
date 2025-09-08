@extends('layouts.app')

@section('title', 'General Settings')

@push('styles')
<style>
    /* CSS Variables for consistent theming */
    :root {
        --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --warning-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        --danger-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
        --glass-bg: rgba(255, 255, 255, 0.25);
        --glass-border: rgba(255, 255, 255, 0.18);
        --shadow-light: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
        --shadow-medium: 0 15px 35px rgba(0, 0, 0, 0.1);
        --shadow-heavy: 0 25px 50px rgba(0, 0, 0, 0.15);
        --border-radius: 20px;
        --transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    /* Animated background with floating particles */
    .settings-container {
        background: var(--primary-gradient);
        min-height: 100vh;
        padding: 2rem 0;
        position: relative;
        overflow: hidden;
    }

    .settings-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: 
            radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
            radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 40% 40%, rgba(120, 119, 198, 0.2) 0%, transparent 50%);
        animation: float 20s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(5deg); }
    }

    /* Floating particles animation */
    .settings-container::after {
        content: '';
        position: absolute;
        width: 100%;
        height: 100%;
        background-image: 
            radial-gradient(2px 2px at 20px 30px, rgba(255,255,255,0.3), transparent),
            radial-gradient(2px 2px at 40px 70px, rgba(255,255,255,0.2), transparent),
            radial-gradient(1px 1px at 90px 40px, rgba(255,255,255,0.4), transparent),
            radial-gradient(1px 1px at 130px 80px, rgba(255,255,255,0.3), transparent);
        background-repeat: repeat;
        background-size: 200px 200px;
        animation: sparkle 15s linear infinite;
        pointer-events: none;
    }

    @keyframes sparkle {
        from { transform: translateY(0px); }
        to { transform: translateY(-200px); }
    }
    
    /* Enhanced glass morphism card */
    .settings-card {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        border-radius: var(--border-radius);
        box-shadow: var(--shadow-light);
        overflow: hidden;
        position: relative;
        z-index: 1;
    }

    .settings-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    }
    
    /* Premium header with gradient text */
    .settings-header {
        background: var(--primary-gradient);
        color: white;
        padding: 3rem 2rem;
        border: none;
        position: relative;
        overflow: hidden;
    }

    .settings-header::before {
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
    
    .settings-header h3 {
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
    
    /* Enhanced form sections with 3D effects */
    .form-section {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: var(--border-radius);
        padding: 2.5rem;
        margin-bottom: 2rem;
        box-shadow: var(--shadow-medium);
        transition: var(--transition);
        position: relative;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .form-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: var(--border-radius);
        padding: 2px;
        background: var(--primary-gradient);
        mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        mask-composite: exclude;
        opacity: 0;
        transition: var(--transition);
    }
    
    .form-section:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: var(--shadow-heavy);
    }
    
    .form-section:hover::before {
        opacity: 1;
    }
    
    /* Premium section headers */
    .form-section h4 {
        color: #2d3748;
        font-weight: 700;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 3px solid transparent;
        background: var(--primary-gradient);
        background-clip: padding-box;
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
        background: var(--primary-gradient);
        border-radius: 2px;
        animation: expand 0.8s ease-out;
    }

    @keyframes expand {
        from { width: 0; }
        to { width: 60px; }
    }
    
    /* Enhanced form groups */
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
        background: var(--primary-gradient);
        border-radius: 1px;
        opacity: 0;
        transition: var(--transition);
    }

    .form-group:focus-within label::after {
        opacity: 1;
        width: 100px;
    }
    
    /* Premium form controls */
    .form-control {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem 1.2rem;
        font-size: 1rem;
        transition: var(--transition);
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

    .form-control.is-valid {
        border-color: transparent;
        background: rgba(240, 253, 244, 0.9);
        box-shadow: 
            0 0 0 3px rgba(34, 197, 94, 0.1),
            0 10px 25px rgba(34, 197, 94, 0.15);
    }
    
    /* Premium buttons */
    .btn-primary {
        background: var(--primary-gradient);
        border: none;
        border-radius: 12px;
        padding: 1rem 2.5rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: var(--transition);
        box-shadow: var(--shadow-medium);
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
        box-shadow: var(--shadow-heavy);
    }

    .btn-primary:active {
        transform: translateY(-1px) scale(1.02);
    }
    
    .btn-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        border: none;
        border-radius: 12px;
        padding: 1rem 2.5rem;
        font-weight: 700;
        transition: var(--transition);
        box-shadow: var(--shadow-medium);
        position: relative;
        overflow: hidden;
    }

    .btn-secondary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.6s ease;
    }

    .btn-secondary:hover::before {
        left: 100%;
    }
    
    .btn-secondary:hover {
        background: linear-gradient(135deg, #5a6268 0%, #343a40 100%);
        transform: translateY(-3px) scale(1.05);
        box-shadow: var(--shadow-heavy);
    }
    
    /* Enhanced feedback */
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

    .valid-feedback {
        color: #38a169;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        font-weight: 600;
        background: rgba(240, 253, 244, 0.8);
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        border-left: 4px solid #38a169;
    }
    
    .text-danger {
        color: #e53e3e !important;
    }
    
    /* Loading states */
    .loading {
        display: none;
    }
    
    .loading.show {
        display: inline-block;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    /* Premium success message */
    .success-message {
        background: var(--success-gradient);
        color: white;
        padding: 1.5rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        display: none;
        box-shadow: var(--shadow-medium);
        position: relative;
        overflow: hidden;
        animation: slideInDown 0.6s ease-out;
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .success-message.show {
        display: block;
    }

    .success-message::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
        animation: shimmer 2s ease-in-out infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    
    /* Responsive design */
    @media (max-width: 768px) {
        .settings-container {
            padding: 1rem 0;
        }
        
        .form-section {
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .settings-header {
            padding: 2rem 1.5rem;
        }
        
        .settings-header h3 {
            font-size: 1.8rem;
        }

        .btn-primary, .btn-secondary {
            padding: 0.875rem 2rem;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 576px) {
        .settings-header h3 {
            font-size: 1.5rem;
        }

        .form-section h4 {
            font-size: 1.2rem;
        }

        .btn-primary, .btn-secondary {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    }

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        :root {
            --glass-bg: rgba(0, 0, 0, 0.25);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        .form-section {
            background: rgba(30, 30, 30, 0.95);
            color: #e2e8f0;
        }

        .form-section h4 {
            color: #e2e8f0;
        }

        .form-group label {
            color: #e2e8f0;
        }

        .form-control {
            background: rgba(40, 40, 40, 0.9);
            border-color: #4a5568;
            color: #e2e8f0;
        }

        .form-control:focus {
            background: rgba(50, 50, 50, 1);
        }
    }
</style>
@endpush

@section('content')
<div class="settings-container">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-10 col-xl-8">
                <div class="card settings-card">
                    <div class="card-header settings-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3><i class="fas fa-cog me-2"></i>General Settings</h3>
                            <a href="{{ route('settings.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back to Settings
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="success-message" id="successMessage">
                            <i class="fas fa-check-circle me-2"></i>
                            Settings updated successfully!
                        </div>
                    <form action="{{ route('settings.general.update') }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <!-- School Information -->
                        <div class="form-section">
                            <h4><i class="fas fa-school me-2"></i>School Information</h4>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="school_name">School Name <span class="text-danger">*</span></label>
                                        <input type="text" 
                                               class="form-control @error('school_name') is-invalid @enderror" 
                                               id="school_name" 
                                               name="school_name" 
                                               value="{{ old('school_name', $school->name ?? $settings['school_name'] ?? '') }}" 
                                               placeholder="Enter school name"
                                               required>
                                        @error('school_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="school_phone">Phone Number <span class="text-danger">*</span></label>
                                        <input type="tel" 
                                               class="form-control @error('school_phone') is-invalid @enderror" 
                                               id="school_phone" 
                                               name="school_phone" 
                                               value="{{ old('school_phone', $school->phone ?? $settings['school_phone'] ?? '') }}" 
                                               placeholder="Enter phone number"
                                               required>
                                        @error('school_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="school_email">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" 
                                               class="form-control @error('school_email') is-invalid @enderror" 
                                               id="school_email" 
                                               name="school_email" 
                                               value="{{ old('school_email', $school->email ?? $settings['school_email'] ?? '') }}" 
                                               placeholder="Enter email address"
                                               required>
                                        @error('school_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="school_address">School Address <span class="text-danger">*</span></label>
                                        <textarea class="form-control @error('school_address') is-invalid @enderror" 
                                                  id="school_address" 
                                                  name="school_address" 
                                                  rows="4" 
                                                  placeholder="Enter complete school address"
                                                  required>{{ old('school_address', $school->address ?? $settings['school_address'] ?? '') }}</textarea>
                                        @error('school_address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="school_website">Website</label>
                                        <input type="url" 
                                               class="form-control @error('school_website') is-invalid @enderror" 
                                               id="school_website" 
                                               name="school_website" 
                                               value="{{ old('school_website', $school->website ?? $settings['school_website'] ?? '') }}" 
                                               placeholder="https://example.com">
                                        @error('school_website')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Preferences -->
                        <div class="form-section">
                            <h4><i class="fas fa-cogs me-2"></i>System Preferences</h4>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="timezone">Timezone <span class="text-danger">*</span></label>
                                        <select class="form-control @error('timezone') is-invalid @enderror" 
                                                id="timezone" 
                                                name="timezone" 
                                                required>
                                            <option value="">Select Timezone</option>
                                            <option value="Africa/Monrovia" {{ old('timezone', $settings['timezone'] ?? '') == 'Africa/Monrovia' ? 'selected' : '' }}>Africa/Monrovia (Liberia)</option>
                                            <option value="UTC" {{ old('timezone', $settings['timezone'] ?? '') == 'UTC' ? 'selected' : '' }}>UTC (Universal Time)</option>
                                            <option value="America/New_York" {{ old('timezone', $settings['timezone'] ?? '') == 'America/New_York' ? 'selected' : '' }}>America/New_York (EST)</option>
                                            <option value="Europe/London" {{ old('timezone', $settings['timezone'] ?? '') == 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT)</option>
                                        </select>
                                        @error('timezone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="date_format">Date Format <span class="text-danger">*</span></label>
                                        <select class="form-control @error('date_format') is-invalid @enderror" 
                                                id="date_format" 
                                                name="date_format" 
                                                required>
                                            <option value="">Select Date Format</option>
                                            <option value="Y-m-d" {{ old('date_format', $settings['date_format'] ?? '') == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD (2024-01-15)</option>
                                            <option value="d-m-Y" {{ old('date_format', $settings['date_format'] ?? '') == 'd-m-Y' ? 'selected' : '' }}>DD-MM-YYYY (15-01-2024)</option>
                                            <option value="m/d/Y" {{ old('date_format', $settings['date_format'] ?? '') == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY (01/15/2024)</option>
                                            <option value="d/m/Y" {{ old('date_format', $settings['date_format'] ?? '') == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY (15/01/2024)</option>
                                        </select>
                                        @error('date_format')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="time_format">Time Format <span class="text-danger">*</span></label>
                                        <select class="form-control @error('time_format') is-invalid @enderror" 
                                                id="time_format" 
                                                name="time_format" 
                                                required>
                                            <option value="">Select Time Format</option>
                                            <option value="H:i" {{ old('time_format', $settings['time_format'] ?? '') == 'H:i' ? 'selected' : '' }}>24 Hour (14:30)</option>
                                            <option value="g:i A" {{ old('time_format', $settings['time_format'] ?? '') == 'g:i A' ? 'selected' : '' }}>12 Hour (2:30 PM)</option>
                                        </select>
                                        @error('time_format')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="currency">Currency <span class="text-danger">*</span></label>
                                        <select class="form-control @error('currency') is-invalid @enderror" 
                                                id="currency" 
                                                name="currency" 
                                                required>
                                            <option value="">Select Currency</option>
                                            <option value="LRD" {{ old('currency', $settings['currency'] ?? '') == 'LRD' ? 'selected' : '' }}>Liberian Dollar (LRD) 🇱🇷</option>
                                            <option value="USD" {{ old('currency', $settings['currency'] ?? '') == 'USD' ? 'selected' : '' }}>US Dollar (USD) 🇺🇸</option>
                                            <option value="EUR" {{ old('currency', $settings['currency'] ?? '') == 'EUR' ? 'selected' : '' }}>Euro (EUR) 🇪🇺</option>
                                            <option value="GBP" {{ old('currency', $settings['currency'] ?? '') == 'GBP' ? 'selected' : '' }}>British Pound (GBP) 🇬🇧</option>
                                        </select>
                                        @error('currency')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="language">Language <span class="text-danger">*</span></label>
                                        <select class="form-control @error('language') is-invalid @enderror" 
                                                id="language" 
                                                name="language" 
                                                required>
                                            <option value="">Select Language</option>
                                            <option value="en" {{ old('language', $settings['language'] ?? '') == 'en' ? 'selected' : '' }}>English 🇺🇸</option>
                                            <option value="fr" {{ old('language', $settings['language'] ?? '') == 'fr' ? 'selected' : '' }}>French 🇫🇷</option>
                                            <option value="es" {{ old('language', $settings['language'] ?? '') == 'es' ? 'selected' : '' }}>Spanish 🇪🇸</option>
                                        </select>
                                        @error('language')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-section">
                            <div class="d-flex justify-content-center gap-3">
                                <button type="submit" class="btn btn-primary btn-lg" id="saveBtn">
                                    <i class="fas fa-save me-2"></i>
                                    <span class="btn-text">Save Settings</span>
                                    <span class="loading spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                </button>
                                <a href="{{ route('settings.index') }}" class="btn btn-secondary btn-lg">
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
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
    const saveBtn = document.getElementById('saveBtn');
    const btnText = saveBtn.querySelector('.btn-text');
    const loading = saveBtn.querySelector('.loading');
    const successMessage = document.getElementById('successMessage');

    // Show success message if redirected from successful save
    @if(session('success'))
        successMessage.classList.add('show');
        setTimeout(() => {
            successMessage.classList.remove('show');
        }, 5000);
    @endif

    // Form submission handling
    form.addEventListener('submit', function(e) {
        // Show loading state
        saveBtn.disabled = true;
        btnText.textContent = 'Saving...';
        loading.classList.add('show');
        
        // Add pulse effect
        saveBtn.style.animation = 'pulse 1s infinite';
    });

    // Input validation feedback
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.checkValidity()) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else {
                this.classList.remove('is-valid');
                this.classList.add('is-invalid');
            }
        });

        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid') && this.checkValidity()) {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });

    // Auto-save draft (localStorage)
    const formData = {};
    inputs.forEach(input => {
        // Load saved data
        const savedValue = localStorage.getItem(`settings_${input.name}`);
        if (savedValue && !input.value) {
            input.value = savedValue;
        }

        // Save on change
        input.addEventListener('input', function() {
            localStorage.setItem(`settings_${this.name}`, this.value);
        });
    });

    // Clear draft on successful save
    @if(session('success'))
        inputs.forEach(input => {
            localStorage.removeItem(`settings_${input.name}`);
        });
    @endif

    // Add smooth scrolling for form sections
    document.querySelectorAll('.form-section h4').forEach(header => {
        header.style.cursor = 'pointer';
        header.addEventListener('click', function() {
            this.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    });
});

// Add pulse animation
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
`;
document.head.appendChild(style);
</script>
@endpush
