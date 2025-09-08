@extends('layouts.app')

@section('title', 'Change Password')

@push('styles')
<style>
    .password-container {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        padding: 2rem 0;
        position: relative;
        overflow: hidden;
    }

    .password-container::before {
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
    
    .password-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        position: relative;
        z-index: 1;
    }

    .password-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 3rem 2rem;
        border: none;
        position: relative;
        overflow: hidden;
    }

    .password-header::before {
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

    .password-header h3 {
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

    .form-control.is-valid {
        border-color: transparent;
        background: rgba(240, 253, 244, 0.9);
        box-shadow: 
            0 0 0 3px rgba(34, 197, 94, 0.1),
            0 10px 25px rgba(34, 197, 94, 0.15);
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

    .password-strength {
        margin-top: 0.5rem;
        font-size: 0.875rem;
    }

    .strength-indicator {
        display: flex;
        gap: 0.25rem;
        margin-top: 0.5rem;
    }

    .strength-bar {
        height: 4px;
        flex: 1;
        border-radius: 2px;
        background: #e2e8f0;
        transition: all 0.3s ease;
    }

    .strength-bar.weak { background: #f56565; }
    .strength-bar.medium { background: #ed8936; }
    .strength-bar.strong { background: #48bb78; }

    @media (max-width: 768px) {
        .password-container {
            padding: 1rem 0;
        }
        
        .form-section {
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .password-header {
            padding: 2rem 1.5rem;
        }
        
        .password-header h3 {
            font-size: 1.8rem;
        }
    }
</style>
@endpush

@section('content')
<div class="password-container">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8 col-xl-6">
                <div class="card password-card">
                    <div class="card-header password-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h3><i class="fas fa-lock me-3"></i>Change Password</h3>
                            <a href="{{ auth()->id() === $user->id && auth()->user()->user_type !== 'admin' ? route('me.profile') : route('users.profile', $user) }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i> Back to Profile
                            </a>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <form action="{{ route('users.change-password.update', $user) }}" method="POST">
                            @csrf
                            
                            <!-- Password Change Form -->
                            <div class="form-section">
                                <h4><i class="fas fa-key me-2"></i>Password Information</h4>
                                
                                @php($isAdminResettingOther = (auth()->user()->user_type ?? null) === 'admin' && $user->id !== auth()->id())
                                @unless($isAdminResettingOther)
                                <div class="form-group">
                                    <label for="current_password">Current Password <span class="text-danger">*</span></label>
                                    <input type="password" 
                                           class="form-control @error('current_password') is-invalid @enderror" 
                                           id="current_password" 
                                           name="current_password" 
                                           placeholder="Enter your current password"
                                           required>
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @endunless

                                <div class="form-group">
                                    <label for="new_password">New Password <span class="text-danger">*</span></label>
                                    <input type="password" 
                                           class="form-control @error('new_password') is-invalid @enderror" 
                                           id="new_password" 
                                           name="new_password" 
                                           placeholder="Enter your new password"
                                           minlength="8"
                                           required>
                                    
                                    <div class="password-strength">
                                        <small class="text-muted">Password must be at least 8 characters long</small>
                                        <div class="strength-indicator">
                                            <div class="strength-bar" id="strength-bar-1"></div>
                                            <div class="strength-bar" id="strength-bar-2"></div>
                                            <div class="strength-bar" id="strength-bar-3"></div>
                                            <div class="strength-bar" id="strength-bar-4"></div>
                                        </div>
                                    </div>
                                    
                                    @error('new_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="new_password_confirmation">Confirm New Password <span class="text-danger">*</span></label>
                                    <input type="password" 
                                           class="form-control @error('new_password_confirmation') is-invalid @enderror" 
                                           id="new_password_confirmation" 
                                           name="new_password_confirmation" 
                                           placeholder="Confirm your new password"
                                           required>
                                    @error('new_password_confirmation')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="form-section">
                                <div class="d-flex justify-content-center gap-3">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-save me-2"></i> {{ $isAdminResettingOther ? 'Reset Password' : 'Change Password' }}
                                    </button>
                                    <a href="{{ auth()->id() === $user->id && auth()->user()->user_type !== 'admin' ? route('me.profile') : route('users.profile', $user) }}" class="btn btn-secondary btn-lg">
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const newPasswordInput = document.getElementById('new_password');
    const strengthBars = [
        document.getElementById('strength-bar-1'),
        document.getElementById('strength-bar-2'),
        document.getElementById('strength-bar-3'),
        document.getElementById('strength-bar-4')
    ];

    newPasswordInput.addEventListener('input', function() {
        const password = this.value;
        const strength = calculatePasswordStrength(password);
        
        // Reset all bars
        strengthBars.forEach(bar => {
            bar.className = 'strength-bar';
        });
        
        // Apply strength classes
        if (strength >= 1) strengthBars[0].classList.add('weak');
        if (strength >= 2) strengthBars[1].classList.add('medium');
        if (strength >= 3) strengthBars[2].classList.add('strong');
        if (strength >= 4) strengthBars[3].classList.add('strong');
    });

    function calculatePasswordStrength(password) {
        let strength = 0;
        
        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        
        return Math.min(strength, 4);
    }
});
</script>
@endsection
