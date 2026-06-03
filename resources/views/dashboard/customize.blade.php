@extends('layouts.app')

@section('title', 'Customize Dashboard')

@section('content')
<div class="container-fluid px-4 py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">
            <i class="fas fa-cog me-2"></i>Customize Dashboard
        </h2>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Back to Dashboard
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Widget Selection -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Dashboard Widgets</h5>
                    <p class="text-muted mb-0 small">Select widgets to display on your dashboard. Drag to reorder.</p>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.update-widgets') }}" method="POST" id="widgetForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label class="form-label fw-bold">Active Widgets</label>
                            <div id="activeWidgets" class="sortable-widgets border rounded p-3 min-h-200">
                                @foreach($settings->dashboard_widgets ?? [] as $widget)
                                <div class="widget-item mb-2 p-2 bg-light rounded d-flex justify-content-between align-items-center" data-widget="{{ $widget }}">
                                    <span>
                                        <i class="fas fa-grip-vertical me-2 text-muted"></i>
                                        {{ ucwords(str_replace('_', ' ', $widget)) }}
                                    </span>
                                    <input type="hidden" name="widgets[]" value="{{ $widget }}">
                                    <button type="button" class="btn btn-sm btn-outline-danger remove-widget">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                @endforeach
                                @if(empty($settings->dashboard_widgets))
                                <p class="text-muted text-center">No widgets selected. Add widgets from the list below.</p>
                                @endif
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Available Widgets</label>
                            <div class="border rounded p-3">
                                @foreach($availableWidgets as $widget)
                                    @if(!in_array($widget, $settings->dashboard_widgets ?? []))
                                    <div class="widget-item mb-2 p-2 bg-light rounded d-flex justify-content-between align-items-center" data-widget="{{ $widget }}">
                                        <span>
                                            <i class="fas fa-plus-circle me-2 text-success"></i>
                                            {{ ucwords(str_replace('_', ' ', $widget)) }}
                                        </span>
                                        <button type="button" class="btn btn-sm btn-outline-success add-widget">
                                            <i class="fas fa-plus"></i> Add
                                        </button>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-outline-warning" id="resetBtn">
                                <i class="fas fa-undo me-2"></i>Reset to Default
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Widgets
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Theme Settings -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Theme Settings</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('dashboard.update-theme') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" name="dark_mode" id="dark_mode" value="1" 
                                       class="form-check-input" 
                                       {{ ($settings->theme['dark_mode'] ?? false) ? 'checked' : '' }}>
                                <label for="dark_mode" class="form-check-label">Dark Mode</label>
                            </div>
                            <small class="form-text text-muted">Toggle dark mode for the entire application.</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>Save Theme
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Info -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">Quick Info</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small mb-2">
                        <i class="fas fa-info-circle me-2"></i>
                        Widgets can be reordered by dragging them up or down.
                    </p>
                    <p class="text-muted small mb-2">
                        <i class="fas fa-info-circle me-2"></i>
                        Some widgets may not be available based on your role.
                    </p>
                    <p class="text-muted small mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Changes take effect immediately after saving.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const activeWidgets = document.getElementById('activeWidgets');
    
    // Initialize sortable for active widgets
    if (activeWidgets) {
        new Sortable(activeWidgets, {
            animation: 150,
            handle: '.fa-grip-vertical',
            ghostClass: 'sortable-ghost',
        });
    }

    // Add widget
    document.querySelectorAll('.add-widget').forEach(btn => {
        btn.addEventListener('click', function() {
            const widgetItem = this.closest('.widget-item');
            const widget = widgetItem.dataset.widget;
            
            // Move to active widgets
            const newItem = widgetItem.cloneNode(true);
            newItem.querySelector('.fa-plus-circle').className = 'fas fa-grip-vertical me-2 text-muted';
            newItem.querySelector('.add-widget').outerHTML = '<button type="button" class="btn btn-sm btn-outline-danger remove-widget"><i class="fas fa-times"></i></button>';
            
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = 'widgets[]';
            hiddenInput.value = widget;
            newItem.appendChild(hiddenInput);
            
            activeWidgets.appendChild(newItem);
            widgetItem.remove();
            
            // Reinitialize sortable
            new Sortable(activeWidgets, {
                animation: 150,
                handle: '.fa-grip-vertical',
                ghostClass: 'sortable-ghost',
            });
        });
    });

    // Remove widget
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-widget')) {
            const widgetItem = e.target.closest('.widget-item');
            const widget = widgetItem.dataset.widget;
            
            // Move back to available widgets
            const availableWidgets = document.querySelector('.col-md-8 .border.rounded.p-3');
            const newItem = widgetItem.cloneNode(true);
            newItem.querySelector('.fa-grip-vertical').className = 'fas fa-plus-circle me-2 text-success';
            newItem.querySelector('.remove-widget').outerHTML = '<button type="button" class="btn btn-sm btn-outline-success add-widget"><i class="fas fa-plus"></i> Add</button>';
            newItem.querySelector('input[type="hidden"]')?.remove();
            
            availableWidgets.appendChild(newItem);
            widgetItem.remove();
        }
    });

    // Reset button
    document.getElementById('resetBtn')?.addEventListener('click', function() {
        if (confirm('Are you sure you want to reset your dashboard to default? This cannot be undone.')) {
            fetch('{{ route("dashboard.reset") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success || true) {
                    location.reload();
                }
            });
        }
    });
});
</script>
<style>
.sortable-widgets {
    min-height: 100px;
}
.sortable-ghost {
    opacity: 0.4;
}
.widget-item {
    cursor: move;
}
</style>
@endpush
@endsection

