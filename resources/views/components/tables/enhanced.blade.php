@props([
    'data',
    'columns',
    'title',
    'searchable' => true,
    'filterable' => true,
    'sortable' => true,
    'paginated' => true,
    'bulkActions' => true,
    'exportable' => true,
    'refreshable' => true,
    'rowActions' => true
])

<div class="enhanced-table-container" x-data="enhancedTable()">
    <!-- Table Header -->
    <div class="table-header mb-3">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h5 class="table-title mb-0">{{ $title }}</h5>
                <small class="text-muted">
                    <span x-text="totalRecords"></span> {{ __('records found') }}
                    @if($data->hasPages())
                        - {{ __('Page') }} <span x-text="currentPage"></span> {{ __('of') }} <span x-text="lastPage"></span>
                    @endif
                </small>
            </div>
            
            <div class="col-md-6">
                <div class="d-flex justify-content-end gap-2">
                    <!-- Refresh Button -->
                    @if($refreshable)
                        <button type="button" 
                                class="btn btn-outline-secondary btn-sm"
                                @click="refreshTable()"
                                :disabled="loading">
                            <i class="fas fa-sync-alt" :class="{ 'fa-spin': loading }"></i>
                            {{ __('Refresh') }}
                        </button>
                    @endif

                    <!-- Export Button -->
                    @if($exportable)
                        <div class="dropdown">
                            <button class="btn btn-outline-success btn-sm dropdown-toggle" 
                                    type="button" 
                                    data-bs-toggle="dropdown">
                                <i class="fas fa-download"></i>
                                {{ __('Export') }}
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="#" @click="exportData('csv')">
                                        <i class="fas fa-file-csv"></i> CSV
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" @click="exportData('excel')">
                                        <i class="fas fa-file-excel"></i> Excel
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="#" @click="exportData('pdf')">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </a>
                                </li>
                            </ul>
                        </div>
                    @endif

                    <!-- Add New Button (if provided) -->
                    @if(isset($addRoute))
                        <a href="{{ $addRoute }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i>
                            {{ __('Add New') }}
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filters -->
    @if($searchable || $filterable)
        <div class="table-filters mb-3">
            <div class="row">
                <!-- Search -->
                @if($searchable)
                    <div class="col-md-4">
                        <div class="search-box">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-search"></i>
                                </span>
                                <input type="text" 
                                       class="form-control" 
                                       placeholder="{{ __('Search...') }}"
                                       x-model="searchTerm"
                                       @input.debounce.500ms="performSearch()">
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Filters -->
                @if($filterable && isset($filters))
                    <div class="col-md-8">
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach($filters as $key => $filter)
                                <select class="form-select form-select-sm" 
                                        style="width: auto;"
                                        x-model="filters.{{ $key }}"
                                        @change="performSearch()">
                                    <option value="">{{ $filter['label'] ?? $key }}</option>
                                    @foreach($filter['options'] as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Bulk Actions -->
    @if($bulkActions)
        <div class="bulk-actions mb-2" 
             x-show="selectedRows.length > 0"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 transform -translate-y-2"
             x-transition:enter-end="opacity-100 transform translate-y-0">
            <div class="alert alert-info d-flex justify-content-between align-items-center">
                <span>
                    <span x-text="selectedRows.length"></span> {{ __('items selected') }}
                </span>
                <div class="d-flex gap-2">
                    <button type="button" 
                            class="btn btn-sm btn-outline-danger"
                            @click="bulkDelete()">
                        <i class="fas fa-trash"></i>
                        {{ __('Delete Selected') }}
                    </button>
                    @if(isset($bulkActions))
                        @foreach($bulkActions as $action)
                            <button type="button" 
                                    class="btn btn-sm btn-outline-primary"
                                    @click="bulkAction('{{ $action['action'] }}')">
                                <i class="{{ $action['icon'] }}"></i>
                                {{ $action['label'] }}
                            </button>
                        @endforeach
                    @endif
                    <button type="button" 
                            class="btn btn-sm btn-outline-secondary"
                            @click="clearSelection()">
                        {{ __('Clear Selection') }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Table -->
    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead class="table-dark">
                <tr>
                    @if($bulkActions)
                        <th width="40">
                            <input type="checkbox" 
                                   @change="toggleSelectAll($event)"
                                   :checked="selectedRows.length === currentPageRows.length">
                        </th>
                    @endif
                    
                    @foreach($columns as $key => $column)
                        <th>
                            @if($sortable && ($column['sortable'] ?? true))
                                <a href="#" 
                                   class="text-white text-decoration-none"
                                   @click="sortBy('{{ $key }}')"
                                   @click.prevent>
                                    {{ $column['title'] }}
                                    <i class="fas fa-sort ms-1" 
                                       :class="{ 
                                           'fa-sort-up': sortColumn === '{{ $key }}' && sortDirection === 'asc',
                                           'fa-sort-down': sortColumn === '{{ $key }}' && sortDirection === 'desc'
                                       }"></i>
                                </a>
                            @else
                                {{ $column['title'] }}
                            @endif
                        </th>
                    @endforeach
                    
                    @if($rowActions)
                        <th width="120">{{ __('Actions') }}</th>
                    @endif
                </tr>
            </thead>
            
            <tbody>
                <!-- Loading State -->
                <template x-if="loading">
                    <tr>
                        <td colspan="{{ count($columns) + ($bulkActions ? 1 : 0) + ($rowActions ? 1 : 0) }}" 
                            class="text-center py-4">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">{{ __('Loading...') }}</span>
                            </div>
                        </td>
                    </tr>
                </template>

                <!-- Empty State -->
                <template x-if="!loading && currentPageRows.length === 0">
                    <tr>
                        <td colspan="{{ count($columns) + ($bulkActions ? 1 : 0) + ($rowActions ? 1 : 0) }}" 
                            class="text-center py-4">
                            <div class="text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <h5>{{ __('No records found') }}</h5>
                                <p>{{ __('Try adjusting your search or filters') }}</p>
                            </div>
                        </td>
                    </tr>
                </template>

                <!-- Data Rows -->
                <template x-for="(row, index) in currentPageRows" :key="row.id">
                    <tr :class="{ 'table-active': selectedRows.includes(row.id) }">
                        @if($bulkActions)
                            <td>
                                <input type="checkbox" 
                                       :value="row.id"
                                       x-model="selectedRows"
                                       @change="updateSelectAllState()">
                            </td>
                        @endif
                        
                        @foreach($columns as $key => $column)
                            <td>
                                <!-- Dynamic column rendering -->
                                <span x-html="renderColumn(row, '{{ $key }}', {{ json_encode($column) }}"></span>
                            </td>
                        @endforeach
                        
                        @if($rowActions)
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" 
                                            class="btn btn-outline-primary btn-sm"
                                            @click="viewRecord(row)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-outline-secondary btn-sm"
                                            @click="editRecord(row)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-outline-danger btn-sm"
                                            @click="deleteRecord(row)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        @endif
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($paginated && $data->hasPages())
        <div class="table-pagination mt-3">
            {{ $data->links() }}
        </div>
    @endif

    <!-- Loading Overlay -->
    <div class="loading-overlay" 
         x-show="loading"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">{{ __('Loading...') }}</span>
        </div>
    </div>
</div>

<script>
function enhancedTable() {
    return {
        loading: false,
        searchTerm: '',
        sortColumn: '',
        sortDirection: 'asc',
        selectedRows: [],
        currentPageRows: @json($data->items()),
        totalRecords: @json($data->total()),
        currentPage: @json($data->currentPage()),
        lastPage: @json($data->lastPage()),
        filters: {
            @foreach($filters ?? [] as $key => $filter)
                '{{ $key }}': '',
            @endforeach
        },

        init() {
            // Initialize any additional functionality
        },

        performSearch() {
            this.loading = true;
            
            // Build query parameters
            const params = new URLSearchParams();
            
            if (this.searchTerm) {
                params.append('search', this.searchTerm);
            }
            
            if (this.sortColumn) {
                params.append('sort', this.sortColumn);
                params.append('direction', this.sortDirection);
            }
            
            Object.keys(this.filters).forEach(key => {
                if (this.filters[key]) {
                    params.append(key, this.filters[key]);
                }
            });

            // Update URL and fetch data
            const newUrl = window.location.pathname + '?' + params.toString();
            window.history.pushState({}, '', newUrl);
            
            // Fetch updated data (implement this based on your API)
            this.fetchTableData(newUrl);
        },

        fetchTableData(url) {
            // Implement actual data fetching logic
            // This would make an AJAX call to your backend
            setTimeout(() => {
                // Simulate API call
                this.loading = false;
            }, 500);
        },

        sortBy(column) {
            if (this.sortColumn === column) {
                this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                this.sortColumn = column;
                this.sortDirection = 'asc';
            }
            
            this.performSearch();
        },

        toggleSelectAll(event) {
            if (event.target.checked) {
                this.selectedRows = this.currentPageRows.map(row => row.id);
            } else {
                this.selectedRows = [];
            }
        },

        updateSelectAllState() {
            // This will be automatically handled by the checkbox binding
        },

        clearSelection() {
            this.selectedRows = [];
        },

        viewRecord(record) {
            // Implement view action
            window.location.href = `/records/${record.id}`;
        },

        editRecord(record) {
            // Implement edit action
            window.location.href = `/records/${record.id}/edit`;
        },

        deleteRecord(record) {
            if (confirm('Are you sure you want to delete this record?')) {
                // Implement delete action
                this.deleteRecordFromServer(record.id);
            }
        },

        bulkDelete() {
            if (confirm(`Are you sure you want to delete ${this.selectedRows.length} records?`)) {
                // Implement bulk delete
                this.bulkDeleteFromServer();
            }
        },

        bulkAction(action) {
            // Implement custom bulk actions
            console.log('Bulk action:', action, this.selectedRows);
        },

        exportData(format) {
            // Implement export functionality
            const params = new URLSearchParams();
            params.append('export', format);
            
            if (this.searchTerm) {
                params.append('search', this.searchTerm);
            }
            
            Object.keys(this.filters).forEach(key => {
                if (this.filters[key]) {
                    params.append(key, this.filters[key]);
                }
            });

            window.open(window.location.pathname + '?' + params.toString());
        },

        refreshTable() {
            this.loading = true;
            this.performSearch();
        },

        renderColumn(row, columnKey, columnConfig) {
            let value = row[columnKey];
            
            // Apply column-specific formatting
            if (columnConfig.type) {
                switch (columnConfig.type) {
                    case 'date':
                        return value ? new Date(value).toLocaleDateString() : '';
                    case 'currency':
                        return value ? '$' + parseFloat(value).toFixed(2) : '';
                    case 'boolean':
                        return value ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>';
                    case 'badge':
                        return `<span class="badge bg-${columnConfig.color || 'primary'}">${value}</span>`;
                    default:
                        return value;
                }
            }
            
            return value || '-';
        },

        deleteRecordFromServer(id) {
            // Implement actual server deletion
            fetch(`/records/${id}`, { method: 'DELETE' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.currentPageRows = this.currentPageRows.filter(row => row.id !== id);
                        this.selectedRows = this.selectedRows.filter(rowId => rowId !== id);
                    }
                });
        },

        bulkDeleteFromServer() {
            // Implement actual bulk deletion
            fetch('/records/bulk-delete', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ ids: this.selectedRows })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.currentPageRows = this.currentPageRows.filter(row => !this.selectedRows.includes(row.id));
                    this.selectedRows = [];
                }
            });
        }
    }
}
</script>

<style>
.enhanced-table-container {
    position: relative;
}

.table-header {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 1rem;
}

.search-box .input-group-text {
    background: #f8f9fa;
    border-right: none;
}

.search-box .form-control {
    border-left: none;
}

.search-box .form-control:focus {
    box-shadow: none;
    border-color: #dee2e6;
}

.bulk-actions .alert {
    margin-bottom: 0;
    animation: slideDown 0.2s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.table-responsive {
    border-radius: 8px;
    overflow: hidden;
}

.table th a {
    transition: color 0.2s;
}

.table th a:hover {
    color: #fff !important;
}

.loading-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(255, 255, 255, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.btn-group-sm .btn {
    padding: 0.25rem 0.5rem;
}

@media (max-width: 768px) {
    .table-header .row {
        flex-direction: column;
        gap: 1rem;
    }
    
    .table-filters .row {
        flex-direction: column;
        gap: 1rem;
    }
    
    .bulk-actions .alert {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .btn-group-sm .btn {
        padding: 0.2rem 0.4rem;
        font-size: 0.75rem;
    }
}
</style>
