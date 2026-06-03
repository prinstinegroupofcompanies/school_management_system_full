@props([
    'tableId' => 'dataTable',
    'ajaxUrl' => null,
    'columns' => [],
    'options' => [],
    'filters' => [],
    'showFilters' => true,
])

@push('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
@endpush

@push('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const tableId = '{{ $tableId }}';
    const ajaxUrl = @json($ajaxUrl);
    const columns = @json($columns);
    const options = @json($options);
    const filters = @json($filters);

    // Load saved preferences
    const savedPrefs = localStorage.getItem(`dt_prefs_${tableId}`);
    const defaultOptions = {
        processing: true,
        serverSide: !!ajaxUrl,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, 'All']],
        order: [[0, 'asc']],
        language: {
            search: 'Search:',
            lengthMenu: 'Show _MENU_ entries',
            info: 'Showing _START_ to _END_ of _TOTAL_ entries',
            infoEmpty: 'No entries found',
            infoFiltered: '(filtered from _MAX_ total entries)',
            zeroRecords: 'No matching records found',
            emptyTable: 'No data available in table',
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        responsive: true,
        autoWidth: false,
        deferRender: true,
    };

    let dtOptions = { ...defaultOptions, ...options };

    // Merge saved preferences
    if (savedPrefs) {
        try {
            const prefs = JSON.parse(savedPrefs);
            if (prefs.pageLength) dtOptions.pageLength = prefs.pageLength;
            if (prefs.order) dtOptions.order = prefs.order;
        } catch (e) {
            console.error('Error parsing saved preferences:', e);
        }
    }

    // Add columns
    if (columns.length > 0) {
        dtOptions.columns = columns;
    }

    // Add AJAX if provided
    if (ajaxUrl) {
        dtOptions.ajax = {
            url: ajaxUrl,
            type: 'GET',
            data: function(d) {
                // Add custom filters
                Object.keys(filters).forEach(key => {
                    const filterEl = document.getElementById(`filter_${key}`);
                    if (filterEl && filterEl.value) {
                        d[key] = filterEl.value;
                    }
                });
            },
        };
    }

    // Initialize DataTable
    const table = $(`#${tableId}`).DataTable(dtOptions);

    // Save preferences on state change
    table.on('page.dt length.dt order.dt', function() {
        const state = table.state();
        const prefs = {
            pageLength: state.length,
            order: state.order,
        };
        localStorage.setItem(`dt_prefs_${tableId}`, JSON.stringify(prefs));
    });

    // Handle filter changes
    Object.keys(filters).forEach(key => {
        const filterEl = document.getElementById(`filter_${key}`);
        if (filterEl) {
            filterEl.addEventListener('change', function() {
                if (ajaxUrl) {
                    table.ajax.reload();
                } else {
                    table.draw();
                }
            });
        }
    });

    // Clear filters function
    window.clearTableFilters = function() {
        Object.keys(filters).forEach(key => {
            const filterEl = document.getElementById(`filter_${key}`);
            if (filterEl) {
                filterEl.value = '';
            }
        });
        if (ajaxUrl) {
            table.ajax.reload();
        } else {
            table.draw();
        }
    };
});
</script>
@endpush

<div class="datatable-wrapper">
    @if($showFilters && count($filters) > 0)
    <div class="card mb-3">
        <div class="card-body">
            <div class="row g-3">
                @foreach($filters as $key => $filter)
                <div class="col-md-3">
                    <label for="filter_{{ $key }}" class="form-label">{{ $filter['label'] ?? ucfirst(str_replace('_', ' ', $key)) }}</label>
                    @if(isset($filter['type']) && $filter['type'] === 'select')
                        <select id="filter_{{ $key }}" class="form-select form-select-sm">
                            <option value="">All</option>
                            @foreach($filter['options'] ?? [] as $optionValue => $optionLabel)
                                <option value="{{ $optionValue }}">{{ $optionLabel }}</option>
                            @endforeach
                        </select>
                    @elseif(isset($filter['type']) && $filter['type'] === 'date')
                        <input type="date" id="filter_{{ $key }}" class="form-control form-control-sm">
                    @else
                        <input type="text" id="filter_{{ $key }}" class="form-control form-control-sm" placeholder="{{ $filter['placeholder'] ?? 'Filter...' }}">
                    @endif
                </div>
                @endforeach
                <div class="col-md-3 d-flex align-items-end">
                    <button class="btn btn-sm btn-outline-secondary" onclick="clearTableFilters()">
                        <i class="fas fa-times me-1"></i> Clear Filters
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="table-responsive">
        <table id="{{ $tableId }}" class="table table-striped table-hover table-bordered">
            {{ $slot }}
        </table>
    </div>
</div>

